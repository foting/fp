package se.uu.it.android.fridaypub;

import android.util.Log;

class FPDBCli
{
    public static FPDBReplyIOU connect()
    {
        String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
        String username = "gurra";
        String password = "gurra";

        FPDBReply<FPDBReplyIOU> reply = null;
        try {
            FPDB db = new FPDB(url, username, password);
            reply = db.iou_get_all();
        } catch (Exception e) {
            Log.i("Error", e.getMessage());
        }

        Log.i("IOU - ", "IOU");
        for (FPDBReplyIOU i : reply.payload) {
        	Log.i("Username: ", i.username);
        	Log.i("First name: ", i.first_name);
        	Log.i("Last name: ", i.last_name);
        	Log.i("Assets: ", i.assets);
        }
        return reply;
    }
}
