package se.uu.it.android.fridaypub;

import java.util.Collection;
import java.util.LinkedList;

import org.json.JSONException;
import org.json.JSONObject;


public class IOUAll extends FPDB
{
    private LinkedList<Reply> payload;

    public IOUAll(String url, String username, String password)
    {
        super(url, username, password);
    }

    public class Reply
    {
        public String username;
        public String first_name;
        public String last_name;
        public float assets;

        public Reply(JSONObject jobj) throws JSONException
        {
            username = jobj.getString("username");
            first_name = jobj.getString("first_name");
            last_name = jobj.getString("last_name");
            assets = Float.parseFloat(jobj.getString("assets"));
        }
    }

    protected void pushReply(JSONObject jobj) throws JSONException
    {
        payload.add(new Reply(jobj));
    }

    public Collection<Reply> get() throws FPDBException
    {
        payload = new LinkedList<Reply>();
        pullPayload(url + "&action=iou_get_all", "iou_get_all");
        return payload;
    }
}

