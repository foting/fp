/*
 * Copyright (C) 2012 The Android Open Source Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
package se.uu.it.android.fridaypub;


public class Ipsum {

    static String[] MenuItems = {
        "Starting Point",
    	"View Inventory",
        "View Bank",
        "Accept Payment",
        "Buy Beer",
        "Restock",
        "Add User",
        "Credentials"
    };

    static String[] Views = {
        "Starting Point view\n\nThis is where you end up when starting, should implement login-view if no user is saved, "+
        		"otherwise show if the pub is open and some statistics or whatever.\n\n",
    	"Inventory view\n\nThis view shows the inventory.\n\n",
        "Bank view\n\nThis view shows the bank.\n\n",
        "Payment view\n\nThis view is for registering payments.\n\n",
        "Buy Beer view\n\nThis view is for registering bought beer.\n\n",
        "Restock view\n\nThis view is for updating the stock of beer.\n\n",
        "Add User view\n\nThis view is for adding new users to the pub.\n\n",
        "User Credentials\n\nThis view provides the ability to set the username and password for the FPDB queries.\n\n"
    };
}
