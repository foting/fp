package se.uu.it.fridaypub;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;

class HttpGet
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
                sr.close(); //XXX No, this does.
            }
        }

        return page;
    }
}


