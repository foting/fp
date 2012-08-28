package se.uu.it.fridaypub;

import org.json.JSONException;
import org.json.JSONObject;

public class EmptyReply
{
    public EmptyReply(JSONObject jobj) throws JSONException
    {
    }
}

class EmptyReplyFactory implements ReplyFactory<EmptyReply>
{
    public EmptyReply create(JSONObject jobj) throws JSONException
    {
        return new EmptyReply(jobj);
    }
}
