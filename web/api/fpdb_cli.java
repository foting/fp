import java.util.*;
import java.io.*; 
import org.json.simple.*;


class FPDB_Error extends Exception
{
    public FPDB_Error(String m)
    {
        super(m);
    }
}

class FPDB_Reply implements Iterable<Map<String, String>>
{
    public String type;
    public List<Map<String, String>> payload;

    public FPDB_Reply(JSONObject jobj) throws FPDB_Error
    {
        type = (String)jobj.get("type");
        if (type == null) {
            throw new FPDB_Error("Reply has no type attribute");
        }

        payload = (List<Map<String, String>>)jobj.get("payload");
        if (payload == null) {
            throw new FPDB_Error("Reply has no payload attribute");
        }
    }

    /* Iterator interface */
    public Iterator<Map<String, String>> iterator()
    {
        return payload.iterator();
    }
}

class FPDB_Api
{
    public static JSONObject http_get(String url) throws FPDB_Error
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
            throw new FPDB_Error(e.getMessage());
        }

            
        jobj = (JSONObject)JSONValue.parse(jstr);
        if (jobj == null) {
            throw new FPDB_Error("Parsing failed");
        }

        return jobj;
    }


    public static FPDB_Reply request(String url) throws FPDB_Error
    {
        FPDB_Reply reply = new FPDB_Reply(http_get(url));
        if (reply.type.equals("error")) {
           throw new FPDB_Error(reply.payload.get(0).get("error"));
        }
        return reply;
    }
}



class FPDB_Cli
{
    public static void main(String[] args)
    {
        FPDB_Reply reply = null;

        if (args.length != 1) {
            System.out.println("Usage: FPDB_Cli url");
            System.exit(-1);
        }

        try {
            reply = FPDB_Api.request(args[0]);
        } catch (FPDB_Error e) {
            System.out.println(e);
            System.exit(-1);
        }

        System.out.println("type: " + reply.type);
        for (Map<String, String> i : reply) {
            System.out.println("  payload:");
            for (Map.Entry<String, String> j : i.entrySet()) {
                System.out.println("    " + j.getKey() + ": " + j.getValue());
            }
        }
    }
}
