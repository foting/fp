package se.uu.it.android.fridaypub;

import android.os.Bundle;
import android.preference.PreferenceActivity;

public class SettingsActivity extends PreferenceActivity {

	public static final String KEY_USERNAME_PREFERENCE = "user_value";
	public static final String KEY_PASSWORD_PREFERENCE = "password_value";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		
		// Display the fragment as the main content.
		getFragmentManager().beginTransaction()
		.replace(android.R.id.content, new SettingsFragment())
		.commit();
	}
}