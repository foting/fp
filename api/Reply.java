package se.uu.it.fridaypub;

import java.util.Iterator;
import java.util.LinkedList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

interface ReplyFactory<T>
{
    public T create(JSONObject jobj) throws JSONException;
}

public class Reply<T> implements Iterable<T>
{
    private LinkedList<T> payload;

    public Reply(String reply, ReplyFactory<T> factory) throws FPDBException
    {
        payload = new LinkedList<T>();
        try {
            JSONObject jobj = new JSONObject(new JSONTokener(reply));

            JSONArray jarr = jobj.getJSONArray("payload");
            for (int i = 0; i < jarr.length(); i++) {
                payload.add(factory.create(jarr.getJSONObject(i)));
            }
        } catch (JSONException e) {
            throw new FPDBException(e);
        }
    }

    public Iterator<T> iterator()
    {
        return payload.iterator();
    }
}


