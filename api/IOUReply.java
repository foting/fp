package se.uu.it.fridaypub;

import org.json.JSONException;
import org.json.JSONObject;

public class IOUReply
{
    public String username;
    public String first_name;
    public String last_name;
    public float assets;

    public IOUReply(JSONObject jobj) throws JSONException
    {
        username = jobj.getString("username");
        first_name = jobj.getString("first_name");
        last_name = jobj.getString("last_name");
        assets = Float.parseFloat(jobj.getString("assets"));
    }
}

class IOUReplyFactory implements ReplyFactory<IOUReply>
{
    public IOUReply create(JSONObject jobj) throws JSONException
    {
        return new IOUReply(jobj);
    }
}

