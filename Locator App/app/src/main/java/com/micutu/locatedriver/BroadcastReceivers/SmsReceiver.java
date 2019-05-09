package com.micutu.locatedriver.BroadcastReceivers;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.telephony.SmsMessage;
import android.widget.Toast;

import com.micutu.locatedriver.R;
import com.micutu.locatedriver.Services.SmsSenderService;
import com.micutu.locatedriver.Utilities.Permissions;

import java.util.ArrayList;

public class SmsReceiver extends BroadcastReceiver {
    private final static String TAG = SmsReceiver.class.getSimpleName();

    public SmsReceiver() {

    }

    /**
     * The function that gets called when a SMS is received
     * @param context
     * @param intent
     */
    @Override
    public void onReceive(Context context, Intent intent) {

        //The keyword we want to check in the received text messages
        String keyword = "ppsitrack";

        if (keyword.length() == 0) {
             return;
        }

        ArrayList<SmsMessage> list = null;
        try {
            //Get the messages we received that has the specified keyword.
            list = getMessagesWithKeyword(keyword, intent.getExtras());
        } catch (Exception e) {
            return;
        }

        if (list.size() == 0) {
            return;
        }

        //Ask for permissions if the user haven't permitted us before.
        if (!Permissions.haveSendSMSAndLocationPermission(context)) {
            try {
                Permissions.setPermissionNotification(context);
            } catch (Exception e) {
                Toast.makeText(context, R.string.send_sms_and_location_permission, Toast.LENGTH_SHORT).show();
            }

            return;
        }

        //Start the service that sends the messages
        Intent newIntent = new Intent(context, SmsSenderService.class);
        newIntent.putExtra("phoneNumber", list.get(0).getOriginatingAddress());
        context.startService(newIntent);
    }

    /**
     * A function that checks the keyword in the text message received
     * @param keyword The keyword we're looking for
     * @param bundle The message we want to search
     * @return returns the messages that have the specified keyword.
     */
    private ArrayList<SmsMessage> getMessagesWithKeyword(String keyword, Bundle bundle) {
        ArrayList<SmsMessage> list = new ArrayList<SmsMessage>();
        if (bundle != null) {
            Object[] pdus = (Object[]) bundle.get("pdus");
            for (int i = 0; i < pdus.length; i++) {
                SmsMessage sms = null;
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                    String format = bundle.getString("format");
                    sms = SmsMessage.createFromPdu((byte[]) pdus[i], format);
                } else {
                    sms = SmsMessage.createFromPdu((byte[]) pdus[i]);
                }

                if (sms.getMessageBody().toString().equals(keyword)) {
                    list.add(sms);
                }
            }
        }
        return list;
    }

}