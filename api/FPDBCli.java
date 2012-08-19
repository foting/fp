import java.util.*;

class FPDBCli
{
    public static void main(String[] args)
    {
        String url = "http://user.it.uu.se/~deklov/fpdb/api.php";
        String username = "gurra";
        String password = "gurra";

        FPDBReplyIOU reply = null;
        try {
            FPDB db = new FPDB(url, username, password);
            reply = db.iou_get_all();
        } catch (Exception e) {
            System.out.println(e.getMessage());
            System.exit(-1);
        }

        System.out.println("IOU");
        for (_FPDBReplyIOU i : reply.payload) {
            System.out.println("username: " + i.username);
            System.out.println("first_name: " + i.first_name);
            System.out.println("last_name: " + i.last_name);
            System.out.println("assets: " + i.assets);
        }
    }
}
