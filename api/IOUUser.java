package se.uu.it.fridaypub;

import java.util.Collection;
import java.util.LinkedList;

import org.json.JSONException;
import org.json.JSONObject;

public class IOUUser extends FPDB 
{
	private LinkedList<Reply> payload;

	public IOUUser(String url, String username, String password) 
	{
		super(url, username, password);
	}

	public class Reply 
	{
		public int user_id;
		public String first_name;
		public String last_name;
		public float assets;

		public Reply(JSONObject jobj) throws JSONException 
		{
			user_id = jobj.getInt("user_id");
			first_name = jobj.getString("first_name");
			last_name = jobj.getString("last_name");
			assets = Float.parseFloat(jobj.getString("assets"));
		}
	}

	protected void pushReply(JSONObject jobj) throws JSONException 
	{
		payload.add(new Reply(jobj));
	}

	public Collection<Reply> get() throws FPDBException 
	{
		payload = new LinkedList<Reply>();
		pullPayload(url + "&action=iou_get", "iou_get");
		return payload;
	}
}
