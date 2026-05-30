import 'dart:convert';

import 'package:http/http.dart'
    as http;

class ApiService {

  // ====================================
  // GANTI NANTI DENGAN IP API TEMANMU
  // ====================================

 static const String baseUrl = "http://192.168.0.167:8000";

  // ====================================
  // GET DATA
  // ====================================

  static Future<List<dynamic>>
      getData(
    String endpoint,
  ) async {

    try {

      final response = await http.get(

        Uri.parse(
          "$baseUrl/$endpoint",
        ),
      );

      if (response.statusCode == 200) {

        return jsonDecode(
          response.body,
        );
      }

      else {

        throw Exception(
          "Gagal mengambil data",
        );
      }
    }

    catch (e) {

      throw Exception(
        "Error API: $e",
      );
    }
  }

  // ====================================
  // POST DATA
  // ====================================

  static Future<dynamic> postData({

    required String endpoint,

    required Map<String, dynamic>
        body,
  }) async {

    try {

      final response = await http.post(

        Uri.parse(
          "$baseUrl/$endpoint",
        ),

        headers: {

          "Content-Type":
              "application/json",
        },

        body: jsonEncode(body),
      );

      return jsonDecode(
        response.body,
      );
    }

    catch (e) {

      throw Exception(
        "Error POST: $e",
      );
    }
  }

  // ====================================
  // LOGIN
  // ====================================

  static Future<dynamic> login({

    required String username,

    required String password,
  }) async {

    return await postData(

      endpoint: "login",

      body: {

        "username": username,

        "password": password,
      },
    );
  }
}