package se.uu.it.android.fridaypub;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.util.LinkedList;
import java.util.ListIterator;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

/*
import android.util.Log;
*/

abstract class FPDB
{
    public LinkedList<Reply> payload;

    abstract class Reply{};
           
    protected abstract Reply createReply(JSONObject jarr) throws JSONException;
    
    public FPDB(String url, String username, String password) throws JSONException, MalformedURLException, IOException, FPDBException
    {
        url = String.format("%s&username=%s&password=%s", url, username, password);
        URLConnection con = (new URL(url)).openConnection();
        InputStreamReader sr = new InputStreamReader(con.getInputStream());
        BufferedReader br = new BufferedReader(sr);

        String line, jstr = "";
        while ((line = br.readLine()) != null) {
            jstr += line;
        }

    	JSONObject jobj = (JSONObject) new JSONTokener(jstr).nextValue();
    	JSONArray jarr = jobj.getJSONArray("payload");
        String type = (String)jobj.get("type");

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
            payload.add(createReply(jarr.getJSONObject(i)));
        }
    	ListIterator<Reply> fpitems = payload.listIterator();
    	while (fpitems.hasNext())
    		System.out.println(fpitems.next());
    }
}


