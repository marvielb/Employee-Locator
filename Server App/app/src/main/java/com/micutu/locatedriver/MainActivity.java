package com.micutu.locatedriver;

import android.app.PendingIntent;
import android.content.ClipData;
import android.content.ClipboardManager;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.content.res.ColorStateList;
import android.database.Cursor;
import android.net.Uri;
import android.preference.PreferenceManager;
import android.provider.ContactsContract;
import android.support.v4.view.TintableBackgroundView;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.telephony.SmsManager;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.location.places.Place;
import com.google.firebase.iid.FirebaseInstanceId;
import com.google.gson.Gson;
import com.micutu.locatedriver.BroadcastReceivers.SmsReceiver;
import com.micutu.locatedriver.Model.LDPlace;
import com.micutu.locatedriver.Utilities.Permissions;

import java.util.ArrayList;


public class MainActivity extends AppCompatActivity {

    private static final String TAG = "MainActivity";

    private Boolean running = null;
    private String keyword = null;
    private LDPlace place = null;
    public static TextView debugTextView = null;
    public static ClipboardManager clipboard = null;
    private Button btnShowToken = null;
    public static EditText url_EditText = null;


    /**
     * The function that gets called once the app is started.
     * @param savedInstanceState
     */
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Permissions.checkAndRequestPermissions(this); //check marshmallow permissions
        setContentView(R.layout.activity_main);
        restoreSavedData(); //restore keyword, destination, running
        setupToolbar();
        initApp();
        updateUI();
        toggleBroadcastReceiver(); //set broadcast receiver for sms
        scrollTop();
        debugTextView = (TextView)findViewById(R.id.debug_TextView);
        url_EditText = (EditText)findViewById(R.id.url_EditText);
        btnShowToken = (Button)findViewById(R.id.showToken_button);
        clipboard = (ClipboardManager) getSystemService(CLIPBOARD_SERVICE);

         btnShowToken.setOnClickListener(new View.OnClickListener() {
             @Override
             public void onClick(View v) {
                    String token = FirebaseInstanceId.getInstance().getToken();

                 ClipData clip = ClipData.newPlainText(token,token);
                 MainActivity.clipboard.setPrimaryClip(clip);

                    Toast.makeText(MainActivity.this, "Token copied to clipboard", Toast.LENGTH_SHORT).show();

             }
         });

    }



    private void scrollTop() {
        final ScrollView scrollView = (ScrollView) this.findViewById(R.id.scrollview);

        scrollView.post(new Runnable() {
            public void run() {
                scrollView.scrollTo(0, 0);
            }
        });
    }

    private void clearFocus() {
        View current = getCurrentFocus();
        if (current != null) {
            current.clearFocus();
        }

        InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
        imm.hideSoftInputFromWindow(findViewById(android.R.id.content).getWindowToken(), 0);
    }

    private void updateUI() {
        ((Button) this.findViewById(R.id.running_button)).setText((running) ? getResources().getString(R.string.stop) : getResources().getString(R.string.start));
        ((TintableBackgroundView) (Button) this.findViewById(R.id.running_button)).setSupportBackgroundTintList(ColorStateList.valueOf(getResources().getColor((running) ? R.color.colorAccent : R.color.colorPrimary)));
       // ((TintableBackgroundView) (Button) this.findViewById(R.id.send_button)).setSupportBackgroundTintList(ColorStateList.valueOf(getResources().getColor(R.color.colorPrimary)));
    }

    private void toggleRunning() {
        String currentKeyword = "ppsitrack";
        if (currentKeyword.length() == 0 && this.running == false) {
            //can't start application with no keyword
            Toast.makeText(getApplicationContext(), getResources().getString(R.string.error_no_keyword), Toast.LENGTH_SHORT).show();
            return;
        }

        if (this.running == false && !Permissions.haveSendSMSAndLocationPermission(MainActivity.this)) {
            Permissions.requestSendSMSAndLocationPermission(MainActivity.this);
            Toast.makeText(getApplicationContext(), R.string.send_sms_and_location_permission, Toast.LENGTH_SHORT).show();
            return;
        }

        this.running = !this.running;
        saveData();
        updateUI();
        toggleBroadcastReceiver();
    }

    private void toggleBroadcastReceiver() {
        ComponentName receiver = new ComponentName(getApplicationContext(), SmsReceiver.class);
        PackageManager pm = getApplicationContext().getPackageManager();

        pm.setComponentEnabledSetting(receiver,
                (running) ? PackageManager.COMPONENT_ENABLED_STATE_ENABLED : PackageManager.COMPONENT_ENABLED_STATE_DISABLED,
                PackageManager.DONT_KILL_APP);
    }

    private void initApp() {
        initRunningButton();

    }



    private void stop() {
        if (this.running) {
            this.toggleRunning();
        }
    }



    /* read the contact from the contact picker */
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (data != null) {
            Uri uri = data.getData();

            if (uri != null) {
                Cursor c = null;
                try {
                    c = getContentResolver().query(uri, new String[]{
                                    ContactsContract.CommonDataKinds.Phone.NUMBER,
                                    ContactsContract.CommonDataKinds.Phone.TYPE},
                            null, null, null);

                    if (c != null && c.moveToFirst()) {
                        String number = c.getString(0);
                        int type = c.getInt(1);
                        launchService(number);
                    }
                } finally {
                    if (c != null) {
                        c.close();
                    }
                }
            }
        }
    }

    private void launchService(final String number) {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        builder.setPositiveButton(R.string.send, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {

                if (!Permissions.haveSendSMSAndLocationPermission(MainActivity.this)) {
                    Permissions.requestSendSMSAndLocationPermission(MainActivity.this);
                    Toast.makeText(getApplicationContext(), R.string.send_sms_and_location_permission, Toast.LENGTH_SHORT).show();
                    return;
                }


            }
        });
        builder.setNegativeButton(R.string.cancel, null);
        builder.setMessage(this.getResources().getString(R.string.are_you_sure) + " " + number + "?");
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    private void initRunningButton() {
        Button runningButton = (Button) this.findViewById(R.id.running_button);

        runningButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                MainActivity.this.toggleRunning();
                MainActivity.this.clearFocus();
            }
        });
    }




    private void saveData() {
        this.keyword = "ppsitrack";

        SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(this);
        SharedPreferences.Editor editor = settings.edit();
        editor.putBoolean("running", this.running);
        editor.putString("keyword", this.keyword);

        Gson gson = new Gson();
        editor.putString("place", gson.toJson(place, LDPlace.class));

        editor.commit();
    }

    private void restoreSavedData() {
        PreferenceManager.setDefaultValues(this, R.xml.settings_preferences, false);

        SharedPreferences settings = PreferenceManager.getDefaultSharedPreferences(this);

        this.running = settings.getBoolean("running", false);
        this.keyword = settings.getString("keyword", "");

        String json = settings.getString("place", "");
        Gson gson = new Gson();
        this.place = gson.fromJson(json, LDPlace.class);
    }

    private void setPlace(Place place) {
        if (place == null) {
            this.place = null;
        } else {
            this.place = new LDPlace(place.getName() + "", place.getId(), place.getLatLng().latitude, place.getLatLng().longitude);
        }

        this.saveData();
    }

    protected void setupToolbar() {
        final Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
    }
}
