package se.uu.it.android.fridaypub;

/*import android.util.Log;*/

class FPDBCli
{
	static final String IOU = "IOU";
	static final String IOU_ALL = "IOU all";
	static final String INVENTORY = "Inventory";
	
    public void get(final String type)
    {
        String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
        String username = "gurra";
        String password = "gurra";

	        try {
	        	if (type == FPDBCli.IOU)
	        		new IOU(url, username, password);
	        	else if (type == FPDBCli.IOU_ALL)
	        		new IOUAll(url, username, password);
	        	else
	        		new Inventory(url, username, password);
	
	        } catch (Exception e) {
	        	System.out.println("Error: " + e.getMessage());
	        	//Log.i("Error", e.getMessage());
	        }

        /*Log.i("IOU - ", "IOU");
        for (FPDBReplyIOU i : reply.payload) {
        	Log.i("Username: ", i.username);
        	Log.i("First name: ", i.first_name);
        	Log.i("Last name: ", i.last_name);
        	Log.i("Assets: ", i.assets);
        }*/
    }
    
    public static void main(String args[]) {
    	FPDBCli fridayPub = new FPDBCli();
    	//Fultester. MŒste gšra asynkrona anrop.
    	fridayPub.get(FPDBCli.IOU);
    	System.out.println("--------");
    	fridayPub.get(FPDBCli.IOU_ALL);
    	System.out.println("--------");
    	fridayPub.get(FPDBCli.INVENTORY);
    }
}
