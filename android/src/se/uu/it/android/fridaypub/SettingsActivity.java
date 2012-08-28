package se.uu.it.android.fridaypub;

import android.content.SharedPreferences;
import android.content.SharedPreferences.OnSharedPreferenceChangeListener;
import android.os.Bundle;
import android.preference.EditTextPreference;
import android.preference.PreferenceActivity;
import android.preference.PreferenceManager;

public class SettingsActivity extends PreferenceActivity implements OnSharedPreferenceChangeListener {

	public static final String KEY_USERNAME_PREFERENCE = "user_value";
	public static final String KEY_PASSWORD_PREFERENCE = "password_value";

	private EditTextPreference mUsernamePreference;
	private EditTextPreference mPasswordPreference;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		SharedPreferences sharedPref = PreferenceManager.getDefaultSharedPreferences(this);
		
		// Display the fragment as the main content.
		getFragmentManager().beginTransaction()
		.replace(android.R.id.content, new SettingsFragment())
		.commit();

		// Get a reference to the preferences
		mUsernamePreference = (EditTextPreference).findPreference(KEY_USERNAME_PREFERENCE);
		mPasswordPreference = (EditTextPreference).findPreference(KEY_PASSWORD_PREFERENCE);
	}

	@Override
	protected void onResume() {
		super.onResume();

		// Setup the initial values
		mUsernamePreference.setSummary("Current value: " + sharedPref.getValue(KEY_USERNAME_PREFERENCE, ""));
		mPasswordPreference.setSummary("Current value: " + sharedPref.getValue(KEY_PASSWORD_PREFERENCE, "")); 

		// Set up a listener whenever a key changes            
		getSharedPreferences().registerOnSharedPreferenceChangeListener(this);
	}

	@Override
	protected void onPause() {
		super.onPause();

		// Unregister the listener whenever a key changes            
		getSharedPreferences().unregisterOnSharedPreferenceChangeListener(this);    
	}

	public void onSharedPreferenceChanged(SharedPreferences sharedPreferences, String key) {
		// Let's do something a preference value changes
		if (key.equals(KEY_USERNAME_PREFERENCE)) {
			mUsernamePreference.setSummary("Current value: " + sharedPreferences.getvalue(KEY_USERNAME_PREFERENCE, ""));
		}
		else if (key.equals(KEY_PASSWORD_PREFERENCE)) {
			mPasswordPreference.setSummary("Current value: " + sharedPreferences.getValue(KEY_PASSWORD_PREFERENCE, "")); 
		}
	}
}