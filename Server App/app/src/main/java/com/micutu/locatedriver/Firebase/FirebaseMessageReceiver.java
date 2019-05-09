package com.micutu.locatedriver.Firebase;

import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.net.Uri;
import android.support.v4.app.NotificationCompat;
import android.telephony.SmsManager;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;
import com.micutu.locatedriver.MainActivity;
import com.micutu.locatedriver.R;

import java.util.ArrayList;

public class FirebaseMessageReceiver extends FirebaseMessagingService {
    private static final String TAG = "FirebaseMessageReceiver";
    private Context context = null;


    /**
     * A function that gets called when a firebase message is received.
     * @param remoteMessage
     */
    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {

        //Set the context as this current instance of this class
        this.context = this;

        //If the message is not null
        if(remoteMessage.getNotification() != null) {
            //Send the "ppsitrack" keyword message to the received number from the website
           // sendSMS(remoteMessage.getNotification().getBody(), "ppsitrack");
            //Print the notification message for debugging purposes.
            sendNotification(remoteMessage.getNotification().getBody());

            SmsManager smsManager = SmsManager.getDefault();
            smsManager.sendTextMessage(remoteMessage.getNotification().getBody(), null, "ppsitrack", null, null);
        }


    }

    /**
     * Prints the notification
     * @param body The message you want to print
     */
    private void sendNotification(String body) {
        Intent intent = new Intent(this, MainActivity.class );
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);

        PendingIntent pendingIntent = PendingIntent.getActivity(this, 0, intent, PendingIntent.FLAG_ONE_SHOT);
        //Set sound
        Uri notificationSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
        NotificationCompat.Builder notifBuilder = new NotificationCompat.Builder(this)
                .setSmallIcon(R.mipmap.ic_launcher)
                .setContentTitle("Firebase Cloud Messaging")
                .setContentText(body)
                .setAutoCancel(true)
                .setSound(notificationSound)
                .setContentIntent(pendingIntent);

        NotificationManager notificationManager = (NotificationManager)getSystemService(Context.NOTIFICATION_SERVICE);
        notificationManager.notify(0, notifBuilder.build());
    }

    /**
     * Sends the SMS to the specified number
     * @param phoneNumber The number you want to send a message to
     * @param message The message of the SMS
     */
    public void sendSMS(String phoneNumber, String message) {
        //Log.d(TAG, "Send SMS: " + phoneNumber + ", " + message);
        //on samsung intents can't be null. the messages are not sent if intents are null
        ArrayList<PendingIntent> samsungFix = new ArrayList<>();
        samsungFix.add(PendingIntent.getBroadcast(this, 0, new Intent("SMS_RECEIVED"), 0));

        SmsManager smsManager = SmsManager.getDefault();
        ArrayList<String> parts = smsManager.divideMessage(message);
        smsManager.sendMultipartTextMessage(phoneNumber, null, parts, samsungFix, samsungFix);
    }
}
