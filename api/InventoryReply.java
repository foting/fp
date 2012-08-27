package se.uu.it.fridaypub;

import java.util.Collection;

import org.json.JSONException;
import org.json.JSONObject;

public class InventoryReply
{
    public String name;
    public int beer_id;
    public int count;
    public float price;

    public InventoryReply(JSONObject jobj) throws JSONException
    {
        name = jobj.getString("namn");
        beer_id = Integer.parseInt(jobj.getString("beer_id"));
        count = Integer.parseInt(jobj.getString("count"));
        price = Float.parseFloat(jobj.getString("price"));
    }
}

class InventoryReplyFactory implements ReplyFactory<InventoryReply>
{
    public InventoryReply create(JSONObject jobj) throws JSONException
    {
        return new InventoryReply(jobj);
    }
}

