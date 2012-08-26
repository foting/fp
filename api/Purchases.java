package se.uu.it.fridaypub;

import java.util.*;
import org.json.*;


public class Purchases
{
    private String url;

    public Purchases(String url, String username, String password)
    {
        this.url = url;
        this.url += "?username=" + username;
        this.url += "&password=" + password;
        this.url += "&action=purchases_get";
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

    public Collection<Reply> get() throws FPDBException
    {
        FPBackend.Reply reply = (new FPBackend()).get(url);

        if (!reply.type.equals("purchases_get")) {
            throw new FPDBException("Backend protocol error");
        }

        LinkedList<Reply> payload = new LinkedList<Reply>();
        try {
            for (JSONObject o : reply) {
                payload.add(new Reply(o));
            }
        } catch (JSONException e) {
            throw new FPDBException(e);
        }

        return payload;
    }
}

