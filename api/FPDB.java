package se.uu.it.fridaypub;

import java.io.*;
import java.net.*;
import java.util.*;
import org.json.*;


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


class FPDBReply<T> implements Iterable<T>
{
    private LinkedList<T> payload;

    public FPDBReply(String jstr, FPDBReplyFactory<T> factory) throws FPDBException
    {
        JSONObject jobj;
        JSONArray jarr;
        String type;

        try {
            jobj = new JSONObject(new JSONTokener(jstr));

            jarr = jobj.getJSONArray("payload");
            type = (String)jobj.get("type");

            if (type.equals("error")) {
                throw new FPDBException(jarr.getJSONObject(0).getString("msg"));
            }
            
            payload = new LinkedList<T>();
            for (int i = 0; i < jarr.length(); i++) {
                payload.add(factory.create(jarr.getJSONObject(i)));
            }
        } catch (JSONException e) {
            throw new FPDBException(e.getMessage());
        }
    }

    public Iterator<T> iterator()
    {
        return payload.iterator();
    }
}


class FPDBReplyInventory
{
    public String name;
    public int beer_id;
    public int count;
    public float price;

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


class FPDBReplyPurchases
{
    public int user_id;
    public int beer_id;
    public float price;
    public String timestamp;

    public FPDBReplyPurchases(JSONObject jobj) throws JSONException
    {
        user_id = Integer.parseInt(jobj.getString("user_id"));
        beer_id = Integer.parseInt(jobj.getString("beer_id"));
        price = Float.parseFloat(jobj.getString("price"));
        timestamp = jobj.getString("timestamp");
    }
}

class FPDBReplyPurchases_Factory implements FPDBReplyFactory<FPDBReplyPurchases>
{
    public FPDBReplyPurchases create(JSONObject jobj) throws JSONException
    {
        return new FPDBReplyPurchases(jobj);
    }
}


class FPDBReplyIOU
{
    public String username;
    public String first_name;
    public String last_name;
    public float assets;

    public FPDBReplyIOU(JSONObject jobj) throws JSONException
    {
        username = jobj.getString("username");
        first_name = jobj.getString("first_name");
        last_name = jobj.getString("last_name");
        assets = Float.parseFloat(jobj.getString("assets"));
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

    private String http_get_json(String url) throws FPDBException
    {
        String line, jstr = "";
        URLConnection co;
        InputStreamReader sr;
        BufferedReader br;

        try {
            co = (new URL(url)).openConnection();
            sr = new InputStreamReader(co.getInputStream());
            br = new BufferedReader(sr);

            while ((line = br.readLine()) != null) {
                jstr += line;
            }
        } catch (IOException e) {
            new FPDBException(e.getMessage());
        }

        return jstr;
    }

    public FPDBReply<FPDBReplyInventory> inventory_get() throws FPDBException
    {
        String jstr = http_get_json(url + "&action=inventory_get");
        return new FPDBReply<FPDBReplyInventory>(jstr, new FPDBReplyInventory_Factory());
    }

    public FPDBReply<FPDBReplyPurchases> purchases_get() throws FPDBException
    {
        String jstr = http_get_json(url + "&action=iou_get");
        return new FPDBReply<FPDBReplyPurchases>(jstr, new FPDBReplyPurchases_Factory());
    }

    public FPDBReply<FPDBReplyIOU> iou_get() throws FPDBException
    {
        String jstr = http_get_json(url + "&action=iou_get");
        return new FPDBReply<FPDBReplyIOU>(jstr, new FPDBReplyIOU_Factory());
    }

    public FPDBReply<FPDBReplyIOU> iou_get_all() throws FPDBException
    {
        String jstr = http_get_json(url + "&action=iou_get_all");
        return new FPDBReply<FPDBReplyIOU>(jstr, new FPDBReplyIOU_Factory());
    }
}


