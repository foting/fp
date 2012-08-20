package se.uu.it.android.fridaypub;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;
import java.util.LinkedList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

/*
import android.util.Log;
*/

@SuppressWarnings("serial")
class FPDBException extends Exception
{
    public FPDBException(String msg)
    {
        super(msg);
    }
}


interface FPDBReplyFactory<T>
{  
    T create(JSONObject jobj) throws FPDBException, JSONException;
} 


class FPDBReply<T>
{
    public LinkedList<T> payload;

    public FPDBReply(String jstr, FPDBReplyFactory<T> factory) throws FPDBException, JSONException
    {
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

        payload = new LinkedList<T>();
        for (int i = 0; i < jarr.length(); i++) {
            payload.add(factory.create(jarr.getJSONObject(i)));
        }
    }
}


class FPDBReplyInventory
{
    public String name;
    public int    beer_id;
    public int    count;
    public float  price;

    public FPDBReplyInventory(JSONObject jobj) throws JSONException
    {
        name = jobj.getString("name");
        beer_id = Integer.parseInt(jobj.getString("beer_id"));
        count = Integer.parseInt(jobj.getString("count"));
        price = Float.parseFloat(jobj.getString("price"));
    }

}

class FPDBReplyInventory_Factory implements FPDBReplyFactory<FPDBReplyInventory>
{
    public FPDBReplyInventory create(JSONObject jobj) throws JSONException
    {
        return new FPDBReplyInventory(jobj);
    }
}


class FPDBReplyIOU
{
    public String username;
    public String first_name;
    public String last_name;
    public float  assets;

    public FPDBReplyIOU(JSONObject jobj) throws JSONException
    {
        String assets_str;	// XXX Fulkod f�r test
        username = jobj.getString("username");
        first_name = jobj.getString("first_name");
        last_name = jobj.getString("last_name");
        assets_str = jobj.getString("assets");	// XXX Fulkod f�r test
        assets = Float.parseFloat(assets_str);
    }
}

class FPDBReplyIOU_Factory implements FPDBReplyFactory<FPDBReplyIOU>
{
    public FPDBReplyIOU create(JSONObject jobj) throws JSONException
    {
        return new FPDBReplyIOU(jobj);
    }
}


class FPDB
{
    private String url;
    private String username;
    private String password;

    public FPDB(String url, String username, String password)
    {
        this.username = username;
        this.password = password;
        this.url = String.format("%s?username=%s&password=%s", url, this.username, this.password);
    }

    private String http_get_json(String url) throws IOException
    {
        URLConnection con = (new URL(url)).openConnection();
        InputStreamReader sr = new InputStreamReader(con.getInputStream());
        BufferedReader br = new BufferedReader(sr);

        String line, jstr = "";
        while ((line = br.readLine()) != null) {
            jstr += line;
        }

        return jstr;
    }

    public FPDBReply<FPDBReplyInventory> inventory_get() throws FPDBException, IOException, JSONException
    {
        String jstr = http_get_json(url + "&action=inventory_get");
        return new FPDBReply<FPDBReplyInventory>(jstr, new FPDBReplyInventory_Factory());
    }

    public FPDBReply<FPDBReplyIOU> iou_get() throws FPDBException, IOException, JSONException
    {
        String jstr = http_get_json(url + "&action=iou_get");
        return new FPDBReply<FPDBReplyIOU>(jstr, new FPDBReplyIOU_Factory());
    }

    public FPDBReply<FPDBReplyIOU> iou_get_all() throws FPDBException, IOException, JSONException
    {
        String jstr = http_get_json(url + "&action=iou_get_all");
        return new FPDBReply<FPDBReplyIOU>(jstr, new FPDBReplyIOU_Factory());
    }
}


