package se.uu.it.fridaypub;

import java.util.Collection;
import java.util.LinkedList;

import org.json.JSONException;
import org.json.JSONObject;

public class IOU
{
    private String url;

    public IOU(String url, String username, String password)
    {
        this.url = url;
        this.url += "?username=" + username;
        this.url += "&password=" + password;
        this.url += "&action=iou_get_all";
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

    public Collection<Reply> get() throws FPDBException
    {
        FPDB.Reply reply = (new FPDB()).get(url);

        if (!reply.type.equals("iou_get_all")) {
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

