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


abstract class FPDB
{
    protected String url;

    public FPDB(String url, String username, String password)
    {
        this.url = String.format("%s?username=%s&password=%s", url, username, password);
    }

    abstract void pushReply(JSONObject jobj) throws JSONException;
    

    private JSONArray jsonParse(String jstr, String expected_type) throws FPDBException
    {
        JSONArray jarr;

        try {
            JSONObject jobj;
            String type;

            jobj = new JSONObject(new JSONTokener(jstr));

            jarr = jobj.getJSONArray("payload");
            type = (String)jobj.get("type");

            if (type.equals("error")) {
                throw new FPDBException(jarr.getJSONObject(0).getString("msg"));
            }

            if (!type.equals(expected_type)) {
                throw new FPDBException("Backend error: Wrong reply type");
            }

            for (int i = 0; i < jarr.length(); i++) {
                pushReply(jarr.getJSONObject(i));
            }

        } catch (JSONException e) {
            throw new FPDBException(e);
        }

        return jarr;
    }

    protected JSONArray pullPayload(String url, String expected_type) throws FPDBException
    {
        try {
            return jsonParse((new WebPage(url)).get(), expected_type);
        } catch (IOException e) {
            throw new FPDBException(e);
        }
    }
}


