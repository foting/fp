package se.uu.it.fridaypub;

import java.util.*;
import org.json.*;


public class Purchases extends FPDB
{
    private LinkedList<Reply> payload;
    
    public Purchases(String url, String username, String password)
    {
        super(url, username, password);
    }

    public class Reply
    {
        public int user_id;
        public int beer_id;
        public float price;
        public String timestamp;

        public Reply(JSONObject jobj) throws JSONException
        {
            user_id = Integer.parseInt(jobj.getString("user_id"));
            beer_id = Integer.parseInt(jobj.getString("beer_id"));
            price = Float.parseFloat(jobj.getString("price"));
            timestamp = jobj.getString("timestamp");
        }
    }

    protected void pushReply(JSONObject jobj) throws JSONException
    {
        payload.add(new Reply(jobj));
    }

    public Collection<Reply> get() throws FPDBException
    {
        payload = new LinkedList<Reply>();
        pullPayload(url + "&action=purchases_get", "purchases_get");
        return payload;
    }
}

