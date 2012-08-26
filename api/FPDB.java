package se.uu.it.fridaypub;

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
    private String url;

    public WebPage(String url)
    {
        this.url = url;
    }

    public String get() throws IOException
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


class FPBackend
{
    public class Reply
    {
        public String type;
        public JSONArray payload;
    }

    public Reply get(String url) throws FPDBException
    {
        String page;

        try {
            page = (new WebPage(url)).get();
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

abstract class FPDB
{
    protected String url;

    public FPDB(String url, String username, String password)
    {
        this.url = String.format("%s?username=%s&password=%s", url, username, password);
    }

    abstract void pushReply(JSONObject jobj) throws JSONException;
    
    protected void pullPayload(String url, String expected_type) throws FPDBException
    {
        FPBackend.Reply reply = (new FPBackend()).get(url);

        if (!reply.type.equals(expected_type)) {
            throw new FPDBException("Backend protocol error");
        }

        try {
            for (int i = 0; i < reply.payload.length(); i++) {
                pushReply(reply.payload.getJSONObject(i));
            }
        } catch (JSONException e) {
            throw new FPDBException(e);
        }
    }
}


