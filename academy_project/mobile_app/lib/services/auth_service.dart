import '../models/user_model.dart';

class AuthService {
  final List<UserModel> users = [
    // SISWA
    UserModel(
      nama: "Clarisha",

      username: "siswa",

      password: "123456",

      role: "siswa",

      kelas: "XI RPL 1",

      nis: "2025001",
    ),

    // GURU
    UserModel(
      nama: "Pak Budi",

      username: "guru",

      password: "123456",

      role: "guru",
    ),
  ];

  UserModel? login({required String username, required String password}) {
    try {
      return users.firstWhere(
        (user) => user.username == username && user.password == password,
      );
    } catch (e) {
      return null;
    }
  }
}

  // ===================================
  // LOGIN API
  // (NANTI DIPAKAI)
  // ===================================

  /*
  static Future<UserModel?> loginApi({

    required String username,

    required String password,
  }) async {

    final response =
        await ApiService.postData(

      endpoint: "login",

      body: {

        "username": username,

        "password": password,
      },
    );

    if (response['success']) {

      return UserModel.fromJson(
        response['data'],
      );
    }

    return null;
  }
  */
