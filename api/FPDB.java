package se.uu.it.fridaypub;

import java.util.Iterator;
import java.util.NoSuchElementException;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import java.net.URL;
import java.net.URLConnection;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

class WebPage
{
    public static String get(String url) throws IOException
    {
        String page = "";
        URLConnection co;
        InputStreamReader sr = null;
        BufferedReader br = null;

        try {
            co = (new URL(url)).openConnection();
            sr = new InputStreamReader(co.getInputStream());
            br = new BufferedReader(sr);

            String line;
            while ((line = br.readLine()) != null) {
                page += line;
            }
        } finally {
            if (br != null) {
                br.close(); //XXX Does this close sr?
            }
        }

        return page;
    }
}

class JSONArrayIterator implements Iterator<JSONObject>
{
    private JSONArray array;
    private int position;

    public JSONArrayIterator(JSONArray array)
    {
        this.array = array;
        this.position = 0;
    }

    public boolean hasNext()
    {
        return position < array.length();
    }

    public JSONObject next()
    {
        JSONObject o;
        try {
            o = array.getJSONObject(position++);
        } catch (JSONException e) {
            throw new NoSuchElementException();
        }
        return o;
    }

    public void remove() 
    {
        throw new UnsupportedOperationException();
    }
}

class FPDB
{
    public class Reply implements Iterable<JSONObject>
    {
        public String type;
        public JSONArray payload;

        public Iterator<JSONObject> iterator()
        {
            return new JSONArrayIterator(payload);
        }
    }

    public Reply get(String url) throws FPDBException
    {
        String page;

        try {
            page = WebPage.get(url);
        } catch (IOException e) {
            throw new FPDBException(e);
        }

        Reply reply = new Reply();
        try {
            JSONObject o = new JSONObject(new JSONTokener(page));

            reply.type = o.getString("type");
            reply.payload = o.getJSONArray("payload");
        } catch (JSONException e) {
            throw new FPDBException(e);
        }

        return reply;
    }
}

