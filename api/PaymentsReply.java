package se.uu.it.fridaypub;

import org.json.JSONException;
import org.json.JSONObject;

public class PaymentsReply
{
    public int user_id;
    public int admin_id;
    public int amount;

    public PaymentsReply(JSONObject jobj) throws JSONException
    {
        user_id = Integer.parseInt(jobj.getString("user_id"));
        admin_id = Integer.parseInt(jobj.getString("admin_id"));
        amount = Integer.parseInt(jobj.getString("amount"));
    }
}

class PaymentsReplyFactory implements ReplyFactory<PaymentsReply>
{
    public PaymentsReply create(JSONObject jobj) throws JSONException
    {
        return new PaymentsReply(jobj);
    }
}
