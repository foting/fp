package se.uu.it.fridaypub;

import java.util.Collection;

import org.json.JSONException;
import org.json.JSONObject;

public class PurchasesReply
{
    public int user_id;
    public int beer_id;
    public float price;
    public String timestamp;

    public PurchasesReply(JSONObject jobj) throws JSONException
    {
        user_id = Integer.parseInt(jobj.getString("user_id"));
        beer_id = Integer.parseInt(jobj.getString("beer_id"));
        price = Float.parseFloat(jobj.getString("price"));
        timestamp = jobj.getString("timestamp");
    }
}

class PurchasesReplyFactory implements ReplyFactory<PurchasesReply>
{
    public PurchasesReply create(JSONObject jobj) throws JSONException
    {
        return new PurchasesReply(jobj);
    }
}



