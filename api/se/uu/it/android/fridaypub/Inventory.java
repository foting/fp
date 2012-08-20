package se.uu.it.android.fridaypub;

import java.io.IOException;
import java.net.MalformedURLException;

import org.json.JSONException;
import org.json.JSONObject;

public class Inventory extends FPDB
{
    public Inventory(String url, String userName, String password) throws FPDBException, JSONException, MalformedURLException, IOException {
		super(url + "?action=inventory_get", userName, password);
	}
    
    class Reply extends FPDB.Reply
    {
        public String name;
        public int    beer_id;
        public int    count;
        public float  price;

        public Reply(JSONObject jobj) throws JSONException
        {
            name = jobj.getString("namn");
            beer_id = Integer.parseInt(jobj.getString("beer_id"));
            count = Integer.parseInt(jobj.getString("count"));
            price = Float.parseFloat(jobj.getString("price"));
        }
        
        public String toString() {
        	return name + " (" + beer_id +"), Det finns " + count + " kvar som kostar " + price + "kr.";
        }
    }

    @Override
    protected Reply createReply(JSONObject jarr) throws JSONException {
    	return new Reply(jarr);
    }
}
