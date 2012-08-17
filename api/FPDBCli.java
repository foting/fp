import java.util.*;

class FPDBCli
{
    public static void main(String[] args)
    {
        FPDBReply reply = null;

        if (args.length != 1) {
            System.out.println("Usage: FPDB_Cli url");
            System.exit(-1);
        }

        try {
            reply = FPDB.request(args[0]);
        } catch (FPDBException e) {
            System.out.println(e.getMessage());
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
