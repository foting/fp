import java.util.*;
import java.io.*; 
import org.json.simple.*;


class FPDBException extends Exception
{
    public FPDBException(String m)
    {
        super(m);
    }
}

class FPDBReply implements Iterable<Map<String, String>>
{
    public String type;
    public List<Map<String, String>> payload;

    public FPDBReply(JSONObject jobj) throws FPDBException
    {
        type = (String)jobj.get("type");
        if (type == null) {
            throw new FPDBException("Reply has no type attribute");
        }

        payload = (List<Map<String, String>>)jobj.get("payload");
        if (payload == null) {
            throw new FPDBException("Reply has no payload attribute");
        }
    }

    /* Iterator interface */
    public Iterator<Map<String, String>> iterator()
    {
        return payload.iterator();
    }
}

class FPDB
{
    public static JSONObject http_get(String url) throws FPDBException
    {
        String jstr = "";
        JSONObject jobj;

        try {
            BufferedReader br = new BufferedReader(new FileReader(url));
            /*
            URLConnection con = (new URL(url)).openConnection();
            BufferedReader br = new BufferedReader(
                new InputStreamReader(con.getInputStream()));
            */
            String line;
            while ((line = br.readLine()) != null) {
                jstr += line;
            }
            br.close();
        } catch (IOException e) {
            throw new FPDBException(e.getMessage());
        }
            
        jobj = (JSONObject)JSONValue.parse(jstr);
        if (jobj == null) {
            throw new FPDBException("Parsing failed");
        }

        return jobj;
    }

    public static FPDBReply request(String url) throws FPDBException
    {
        FPDBReply reply = new FPDBReply(http_get(url));
        if (reply.type.equals("error")) {
           throw new FPDBException(reply.payload.get(0).get("error"));
        }
        return reply;
    }
}

