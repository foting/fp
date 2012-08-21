package se.uu.it.android.fridaypub;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.Iterator;
import java.util.LinkedList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

/*
import android.util.Log;
*/

abstract class FPDB implements Iterable<FPDB.Reply>
{
    public LinkedList<Reply> payload;

    abstract class Reply{};
           
    protected abstract Reply createReply(JSONObject jarr) throws JSONException;
    
    public FPDB(String url, String username, String password) throws FPDBException
    {
        url = String.format("%s&username=%s&password=%s", url, username, password);
        URLConnection con;
		try {
			con = (new URL(url)).openConnection();
		} catch (MalformedURLException e) {
			throw new FPDBException(e);
		} catch (IOException e) {
			throw new FPDBException(e);
		}
        InputStreamReader sr;
		try {
			sr = new InputStreamReader(con.getInputStream());
		} catch (IOException e) {
			throw new FPDBException(e);
		}
        BufferedReader br = new BufferedReader(sr);

        String line, jstr = "";
        try {
			while ((line = br.readLine()) != null) {
			    jstr += line;
			}
		} catch (IOException e) {
			throw new FPDBException(e);
		}
        
        JSONObject jobj;
        JSONArray jarr;
        String type;
        try {
	    	jobj = new JSONObject(new JSONTokener(jstr));
	    	jarr = jobj.getJSONArray("payload");
	        type = (String)jobj.get("type");
        } catch (JSONException e) {
        	throw new FPDBException(e);
        }

        /*
        Log.i("JSON", jobj.toString());
        Log.i("Type", jobj.getString("type"));
        Log.i("Payload", jobj.get("payload").toString());
        Log.i("Payload_arr", jarr.toString());
        */
        
        if (type == null) {
            throw new FPDBException("JSON no type attribute");
        }

        if (type.equals("error")) {
            throw new FPDBException("TODO: Get error message");
        }
        
        if (jobj.isNull("payload")) {
            throw new FPDBException("JSON no payload attribute");
        }

        payload = new LinkedList<Reply>();
        for (int i = 0; i < jarr.length(); i++) {
            try {
				payload.add(createReply(jarr.getJSONObject(i)));
			} catch (JSONException e) {
				throw new FPDBException(e);
			}
        }
    }
    public Iterator<Reply> iterator()
    {
        return payload.iterator();
	
    }
}


