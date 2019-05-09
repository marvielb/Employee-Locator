package com.micutu.locatedriver.Services;

import android.app.IntentService;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.location.Location;
import android.location.LocationManager;
import android.preference.PreferenceManager;
import android.provider.Settings;
import android.telephony.SmsManager;

import com.google.gson.Gson;
import com.micutu.locatedriver.Model.LDPlace;
import com.micutu.locatedriver.R;
import com.micutu.locatedriver.Utilities.Network;

import org.json.JSONException;
import org.json.JSONObject;

import java.text.DecimalFormat;
import java.util.ArrayList;

import io.nlopez.smartlocation.OnLocationUpdatedListener;
import io.nlopez.smartlocation.SmartLocation;
import io.nlopez.smartlocation.location.config.LocationParams;
import io.nlopez.smartlocation.location.providers.LocationGooglePlayServicesWithFallbackProvider;

public class SmsSenderService extends IntentService implements OnLocationUpdatedListener {
    private final static String TAG = SmsSenderService.class.getSimpleName();

    private final static int LOCATION_REQUEST_MAX_WAIT_TIME = 60;

    private Resources r = null;
    private Context context = null;
    private String phoneNumber = null;

    private LDPlace place = null;
     private boolean networkSms = false;


    private Location bestLocation = null;
    private long startTime = 0;

    public SmsSenderService() {
        super("SmsSenderService");
    }

    /**
     * Get called when the service started.
     * @param intent
     */
    @Override
    protected void onHandleIntent(Intent intent) {

        //Get the number from the intent
        this.phoneNumber = intent.getExtras().getString("phoneNumber");

        if (this.phoneNumber.length() == 0) {
            return;
        }

        this.context = this;
        this.r = context.getResources();
        //Initialize the sending procedure
        initSending();
    }


    private void initSending() {

        //Send a message to the server that we acknowledge the command
        this.sendAcknowledgeMessage(phoneNumber);

        //set bestLocation to null and start time
        startTime = System.currentTimeMillis() / 1000;
        bestLocation = null;

        SmartLocation.with(context).location(new LocationGooglePlayServicesWithFallbackProvider(context))
                .config(LocationParams.NAVIGATION)
                .start(this);

    }

    public static boolean isLocationFused(Location location) {
        return !location.hasAltitude() || !location.hasSpeed() || location.getAltitude() == 0;
    }

    /**
     * Gets called when the current location of the phone gets updated.
     * @param location
     */
    @Override
    public void onLocationUpdated(Location location) {
        long currentTime = System.currentTimeMillis() / 1000;

        //Don't stop searching for the best location until we are allowed to.
        if (currentTime - startTime < this.LOCATION_REQUEST_MAX_WAIT_TIME) {

            if (bestLocation == null) {
                bestLocation = location;
            }

            //still null? check again
            if (bestLocation == null) {
                return;
            }



            if (!bestLocation.getProvider().equals(LocationManager.GPS_PROVIDER) || bestLocation.getProvider().equals(location.getProvider())) {
                if (location.getAccuracy() < bestLocation.getAccuracy()) {
                    bestLocation = location;
                }
            }


            if (this.isLocationFused(bestLocation)) {
                return;
            }

            if (bestLocation.getAccuracy() > 100) {
                return;
            }
        }


        //Stop the location searching once we found a best location
        SmartLocation.with(context).location().stop();

        if (bestLocation == null) {
            this.sendSMS(phoneNumber, r.getString(R.string.error_getting_location));
            return;
        }

        //Send the essential coordinates and positions to the server
            this.sendLocationMessage(phoneNumber, bestLocation);

       //Send the googlemap link for the server to see
            this.sendGoogleMapsMessage(phoneNumber, bestLocation);

        if (!networkSms) {
            return;
        }

        //Send the address provided by the google API if access to the internet is available
        this.sendNetworkMessage(phoneNumber, bestLocation, place, new OnNetworkMessageSentListener() {
            @Override
            public void onNetworkMessageSent() {
                //Log.d(TAG, "on Network Message Sent");
            }
        });
    }

    /**
     * Converts the boolean value to string
     * @param enabled the variable you want to check
     * @return returns the converted string.
     */
    public String booleanToString(Boolean enabled) {
        return (enabled) ? context.getResources().getString(R.string.enabled) :
                context.getResources().getString(R.string.disabled);
    }

    /**
     * Sends the acknowledgement message to the server
     * @param phoneNumber the phonenumber of the server
     */
    public void sendAcknowledgeMessage(String phoneNumber) {
        Resources r = context.getResources();
        String text = r.getString(R.string.acknowledgeMessage);
        text += " " + r.getString(R.string.network) + " " + this.booleanToString(Network.isNetworkAvailable(context));
        text += ", " + r.getString(R.string.gps) + " " + this.locationToString(context, this.getLocationMode(context));
        SmsSenderService.this.sendSMS(phoneNumber, text);
    }

    /**
     * Sends the location latitude ang longitude to the server
     * @param phoneNumber the phone number you want to send it to
     * @param location the location provided by the API
     */
    public void sendLocationMessage(String phoneNumber, Location location) {
        //Log.d(TAG, "sendLocationMessage()" + location.getAccuracy());
        Resources r = context.getResources();
        Boolean fused = isLocationFused(location);

        DecimalFormat latAndLongFormat = new DecimalFormat("#.######");

        String text = r.getString(fused ? R.string.approximate : R.string.accurate) + " location:\n";


        text += r.getString(R.string.accuracy) + " " + Math.round(location.getAccuracy()) + "m\n";
        text += r.getString(R.string.latitude) + " " + latAndLongFormat.format(location.getLatitude()) + "\n";
        text += r.getString(R.string.longitude) + " " + latAndLongFormat.format(location.getLongitude()) + "";
        text += ";";



        SmsSenderService.this.sendSMS(phoneNumber, text);
    }

    /**
     * Sends the google map link to the specified number
     * @param phoneNumber the phone number you want to send it to
     * @param location the location provided by the API
     */
    public void sendGoogleMapsMessage(String phoneNumber, Location location) {
        //Log.d(TAG, "sendGoogleMapsMessage() " + location.getAccuracy());
        String text = "https://maps.google.com/maps?q=" + location.getLatitude() + "," + location.getLongitude();
        SmsSenderService.this.sendSMS(phoneNumber, text);
    }


    /**
     * Sends the address of the longitude if the internet access is avilable
     * @param phoneNumber the phone number you want to send it to
     * @param location the location provided by the API
     * @param place out param to store the address
     * @param onNetworkMessageSentListener the message to call once the address has been retrieved
     */
    public void sendNetworkMessage(final String phoneNumber, final Location location, final LDPlace place, final OnNetworkMessageSentListener onNetworkMessageSentListener) {
        //Log.d(TAG, "sendNetworkMessage() " + location.getAccuracy());

        if (!Network.isNetworkAvailable(context)) {
            SmsSenderService.this.sendSMS(phoneNumber, r.getString(R.string.no_network));
            onNetworkMessageSentListener.onNetworkMessageSent();
            return;
        }


        //Log.d(TAG, "STARTED NETWORK REQUEST");
        Network.get("https://maps.googleapis.com/maps/api/geocode/json?latlng=" + location.getLatitude() + "," + location.getLongitude(), new Network.OnGetFinishListener() {
            @Override
            public void onGetFinish(String result) {
                //Log.d(TAG, "RESULT ARRIVED");
                try {
                    final String address = new JSONObject(result).getJSONArray("results").getJSONObject(0).getString("formatted_address");
                    final String firstText = r.getString(R.string.address) + " " + address + ". ";

                    if (place == null) {
                        SmsSenderService.this.sendSMS(phoneNumber, firstText);
                        onNetworkMessageSentListener.onNetworkMessageSent();
                        return;
                    }

                    Network.get("https://maps.googleapis.com/maps/api/directions/json?origin=" + location.getLatitude() + "," + location.getLongitude() + "&destination=" + place.getLatitude() + "," + place.getLongitude(), new Network.OnGetFinishListener() {
                        @Override
                        public void onGetFinish(String result) {
                            try {
                                JSONObject j = new JSONObject(result).getJSONArray("routes").getJSONObject(0).getJSONArray("legs").getJSONObject(0);
                                String distance = j.getJSONObject("distance").getString("text");
                                String duration = j.getJSONObject("duration").getString("text");

                                SmsSenderService.this.sendSMS(phoneNumber, firstText + r.getString(R.string.remaining_distance_to) + " " + place.getName() + ": " + distance + ". " + r.getString(R.string.aprox_duration) + " " + duration + ".");
                                onNetworkMessageSentListener.onNetworkMessageSent();
                                return;
                            } catch (Exception e) {
                                //Log.d(TAG, "EXCEPTION E: " + e.getMessage());
                                e.printStackTrace();
                            }
                        }
                    });
                } catch (JSONException e) {
                    e.printStackTrace();
                    //Log.d(TAG, "JSON EXCEPTION");
                }
            }
        });
    }

    public interface OnNetworkMessageSentListener {
        public void onNetworkMessageSent();
    }

    @Override
    public void onCreate() {
        super.onCreate();
        //Log.d(TAG, "onCreate()");
    }

    @Override
    public void onDestroy() {
        //Log.d(TAG, "onDestroy()");
        super.onDestroy();
    }

    public static int getLocationMode(Context context) {
        try {
            return Settings.Secure.getInt(context.getContentResolver(), Settings.Secure.LOCATION_MODE);
        } catch (Settings.SettingNotFoundException e) {
            return -1;
        }
    }

    /**
     * converts the mode param to readable txts
     * @param context the context provided by the class
     * @param mode the mode you want to convert
     * @return returns the string equivalent of the mode
     */
    public static String locationToString(Context context, int mode) {
        switch (mode) {
            case Settings.Secure.LOCATION_MODE_OFF:
                return context.getResources().getString(R.string.location_mode_off);
            case Settings.Secure.LOCATION_MODE_BATTERY_SAVING:
                return context.getResources().getString(R.string.location_battery_saving);
            case Settings.Secure.LOCATION_MODE_SENSORS_ONLY:
                return context.getResources().getString(R.string.locateion_sensors_only);
            case Settings.Secure.LOCATION_MODE_HIGH_ACCURACY:
                return context.getResources().getString(R.string.location_high_accuracy);
            default:
                return "Error";
        }
    }


    /**
     * The function that sends the SMS messages (warning: very slow)
     * @param phoneNumber The phone number you want to send the message to
     * @param message The message of the SMS you want to send
     */
    public void sendSMS(String phoneNumber, String message) {
        ArrayList<PendingIntent> samsungFix = new ArrayList<>();
        samsungFix.add(PendingIntent.getBroadcast(context, 0, new Intent("SMS_RECEIVED"), 0));

        SmsManager smsManager = SmsManager.getDefault();
        ArrayList<String> parts = smsManager.divideMessage(message);
        smsManager.sendMultipartTextMessage(phoneNumber, null, parts, samsungFix, samsungFix);
    }
}

