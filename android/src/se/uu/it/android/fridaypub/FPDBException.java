package se.uu.it.android.fridaypub;

@SuppressWarnings("serial")
public class FPDBException extends Exception
{
    private Throwable cause;

    public FPDBException(String message)
    {
        super(message);
    }

    public FPDBException(Throwable cause)
    {
        super(cause.getMessage());
        this.cause = cause;
    }
    
    public Throwable getCause()
    {
        return this.cause;
    }
}
