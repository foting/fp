package se.uu.it.fridaypub;

import java.util.Collection;
import java.util.LinkedList;

import org.json.JSONException;
import org.json.JSONObject;

public class Inventory extends FPDB {
	private LinkedList<Reply> payload;

	public Inventory(String url, String username, String password) {
		super(url, username, password);
	}

	public class Reply {
		public String name;
		public int beer_id;
		public int count;
		public float price;

		public Reply(JSONObject jobj) throws JSONException {
			name = jobj.getString("namn");
			beer_id = Integer.parseInt(jobj.getString("beer_id"));
			count = Integer.parseInt(jobj.getString("count"));
			price = Float.parseFloat(jobj.getString("price"));
		}
	}

	protected void pushReply(JSONObject jobj) throws JSONException {
		payload.add(new Reply(jobj));
	}

	public Collection<Reply> get() throws FPDBException {
		payload = new LinkedList<Reply>();
		pullPayload(url + "&action=inventory_get", "inventory_get");
		return payload;
	}
}
