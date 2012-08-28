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
        "Starting Point",	// 0
        "View Bank",		// 1
        "View Inventory",	// 2
        "Buy Beer",			// 3
        "Purchase History",	// 4
        "Accept Payment",	// 5
        "Restock",			// 6
        "Add User"			// 7
    };

    static String[] Views = {
        "Starting Point view\n\nThis is where you end up when starting, should implement login-view if no user is saved, "+
        		"otherwise show if the pub is open and some statistics or whatever.\n\n",
        "Bank view\n\nThis view shows the bank.\n\n",
    	"Inventory view\n\nThis view shows the inventory.\n\n",
        "Buy Beer view\n\nThis view is for registering bought beer.\n\n",
        "Purchase History view\n\n",
        "Payment view\n\nThis view is for registering payments.\n\n",
        "Restock view\n\nThis view is for updating the stock of beer.\n\n",
        "Add User view\n\nThis view is for adding new users to the pub.\n\n"
    };
}
