package se.uu.it.android.fridaypub;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;


abstract class FPDB
{
    protected String url;

    public FPDB(String url, String username, String password)
    {
        this.url = String.format("%s?username=%s&password=%s", url, username, password);
    }

    abstract void pushReply(JSONObject jobj) throws JSONException;
    
    private String httpGetJson(String url) throws FPDBException
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
            new FPDBException(e);
        }

        return jstr;
    }

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
        return jsonParse(httpGetJson(url), expected_type);
    }
}


