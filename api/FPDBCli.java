package se.uu.it.fridaypub;

class FPDBCli
{
    public static void main(String args[])
    {
        String url = "http://interact.it.uu.se/hci-dev/fp/fpdb/api.php";
        String username = "gurra";
        String password = "gurra";

        FPDBReply<FPDBReplyIOU> reply = null;
        try {
            FPDB db = new FPDB(url, username, password);
            reply = db.iou_get_all();
        } catch (Exception e) {
            System.out.println(e.getMessage());
        }

        for (FPDBReplyIOU i : reply) {
        	System.out.println("Username: " + i.username);
        	System.out.println("First name: " + i.first_name);
        	System.out.println("Last name: " + i.last_name);
        	System.out.println("Assets: "+ i.assets);
        }
    }
}
