package se.uu.it.android.fridaypub;

import java.io.IOException;
import java.net.MalformedURLException;

import org.json.JSONException;
import org.json.JSONObject;

public class IOU extends FPDB
{
    public IOU(String url, String userName, String password) throws FPDBException, JSONException, MalformedURLException, IOException {
		super(url + "?action=iou_get", userName, password);
	}
    
    class Reply extends FPDB.Reply
    {
        public String user_id;
        public String first_name;
        public String last_name;
        public float  assets;

        public Reply(JSONObject jobj) throws JSONException
        {
	        String assets_str;	// XXX Fulkod för test
	        user_id = jobj.getString("user_id");
	        first_name = jobj.getString("first_name");
	        last_name = jobj.getString("last_name");
	        assets_str = jobj.getString("assets");	// XXX Fulkod för test
	        assets = Float.parseFloat(assets_str);
        }
        
        public String toString() {
        	return first_name +" " + last_name + " (anv.id: "+ user_id +") har " + assets + " kr på banken.";
        }
    }

    @Override
    protected Reply createReply(JSONObject jarr) throws JSONException {
    	return new Reply(jarr);
    }
}