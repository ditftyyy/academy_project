class UserModel {

  final String username;

  final String password;

  final String role;

  final String? nama;

  final String? kelas;

  final String? nis;

  final String? foto;

  final List<String>? mapel;

  UserModel({

    required this.username,

    required this.password,

    required this.role,

    this.nama,

    this.kelas,

    this.nis,

    this.foto,

    this.mapel,
  });

  // =========================
  // FROM JSON
  // =========================

  factory UserModel.fromJson(
    Map<String, dynamic> json,
  ) {

    return UserModel(

      username:
          json["username"] ?? "",

      password:
          json["password"] ?? "",

      role:
          json["role"] ?? "",

      nama:
          json["nama"],

      kelas:
          json["kelas"],

      nis:
          json["nis"],

      foto:
          json["foto"],

      mapel:
          json["mapel"] != null

              ? List<String>.from(
                  json["mapel"],
                )

              : [],
    );
  }

  // =========================
  // TO JSON
  // =========================

  Map<String, dynamic> toJson() {

    return {

      "username": username,

      "password": password,

      "role": role,

      "nama": nama,

      "kelas": kelas,

      "nis": nis,

      "foto": foto,

      "mapel": mapel,
    };
  }
}