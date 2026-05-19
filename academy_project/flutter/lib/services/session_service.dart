import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

import '../models/user_model.dart';

class SessionService {

  static UserModel? currentUser;

  // ===================================
  // SIMPAN USER
  // ===================================

  static Future<void> setUser(
    UserModel user,
  ) async {

    currentUser = user;

    final prefs =
        await SharedPreferences
            .getInstance();

    await prefs.setString(

      "user",

      jsonEncode(
        user.toJson(),
      ),
    );
  }

  // ===================================
  // GET USER
  // ===================================

  static UserModel? getUser() {

    return currentUser;
  }

  // ===================================
  // LOAD USER
  // ===================================

  static Future<void> loadUser()
      async {

    final prefs =
        await SharedPreferences
            .getInstance();

    final userString =
        prefs.getString("user");

    if (userString != null) {

      final userJson =
          jsonDecode(userString);

      currentUser =
          UserModel.fromJson(
        userJson,
      );
    }
  }

  // ===================================
  // CHECK LOGIN
  // ===================================

  static bool isLoggedIn() {

    return currentUser != null;
  }

  // ===================================
  // LOGOUT
  // ===================================

  static Future<void> logout()
      async {

    currentUser = null;

    final prefs =
        await SharedPreferences
            .getInstance();

    await prefs.remove("user");
  }
}