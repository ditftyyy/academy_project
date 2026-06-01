import 'package:mongo_dart/mongo_dart.dart';
import 'package:flutter/foundation.dart';

class MongoDBService {
  static Db? _db;

  static Future<Db> get db async {
    if (_db != null) return _db!;
    _db = await Db.create("mongodb://localhost:27017/academy");
    await _db!.open();
    debugPrint('Connected to MongoDB');
    return _db!;
  }

  static Future<void> insertData(Map<String, dynamic> data) async {
    final db = await MongoDBService.db;
    final collection = db.collection('users');
    await collection.insert(data);
  }

  static Future<List<Map<String, dynamic>>> fetchData() async {
    final db = await MongoDBService.db;
    final collection = db.collection('users');
    final data = await collection.find().toList();
    return data;
  }

  static Future<void> close() async {
    if (_db != null) await _db!.close();
  }
}
