package se.uu.it.fridaypub;

import java.util.Collection;
import java.util.LinkedList;

import org.json.JSONException;
import org.json.JSONObject;

public class Inventory
{
    private String url;

	public Inventory(String url, String username, String password)
    {
        this.url = url;
        this.url += "?username=" + username;
        this.url += "&password=" + password;
        this.url += "&action=inventory_get";
	}

	public class Reply
    {
		public String name;
		public int beer_id;
		public int count;
		public float price;

		public Reply(JSONObject jobj) throws JSONException
        {
			name = jobj.getString("namn");
			beer_id = Integer.parseInt(jobj.getString("beer_id"));
			count = Integer.parseInt(jobj.getString("count"));
			price = Float.parseFloat(jobj.getString("price"));
		}
	}


	public Collection<Reply> get() throws FPDBException
    {
        FPBackend.Reply reply = (new FPBackend()).get(url);

        if (!reply.type.equals("inventory_get")) {
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
