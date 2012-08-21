package se.uu.it.android.fridaypub;

import java.util.Iterator;

/*import android.util.Log;*/

class FPDBCLI
{
	static final String IOU = "IOU";
	static final String IOU_ALL = "IOU all";
	static final String INVENTORY = "Inventory";
	
    public Iterator<FPDB.Reply> get(final String type)
    {
        String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
        String username = "gurra";
        String password = "gurra";

        try {
        	if (type == FPDBCLI.IOU)
        		return new IOU(url, username, password).iterator();
        	else if (type == FPDBCLI.IOU_ALL)
        		return new IOUAll(url, username, password).iterator();
        	else
        		return new Inventory(url, username, password).iterator();

        } catch (Exception e) {
        	System.out.println("Error: " + e.getMessage());
        	//Log.i("Error", e.getMessage());
        }
        
        return null;

        /*Log.i("IOU - ", "IOU");
        for (FPDBReplyIOU i : reply.payload) {
        	Log.i("Username: ", i.username);
        	Log.i("First name: ", i.first_name);
        	Log.i("Last name: ", i.last_name);
        	Log.i("Assets: ", i.assets);
        }*/
    }
    
    public static void main(String args[]) {
    	FPDBCLI fridayPub = new FPDBCLI();
    	//Fultester
    	
    	Iterator<FPDB.Reply> iou = fridayPub.get(FPDBCLI.IOU);
    	while (iou.hasNext())
    		System.out.println(iou.next());
    	System.out.println("--------");
    	iou = fridayPub.get(FPDBCLI.IOU_ALL);
    	while (iou.hasNext())
    		System.out.println(iou.next());
    	System.out.println("--------");
    	Iterator<FPDB.Reply> inv = fridayPub.get(FPDBCLI.INVENTORY);
    	while (inv.hasNext())
    		System.out.println(inv.next());
    }
}
