import 'package:dio/dio.dart';

import 'auth_exception.dart';
import 'auth_repository.dart';

class SanctumAuthRepository implements AuthRepository {
  SanctumAuthRepository(this._dio);

  final Dio _dio;

  @override
  Future<String> login({
    required String email,
    required String password,
  }) {
    return _postToken(
      '/auth/login',
      {
        'email': email,
        'password': password,
      },
      fallback: 'Could not sign in.',
    );
  }

  @override
  Future<String> register({
    required String name,
    required String email,
    required String password,
    String? passwordConfirmation,
  }) {
    return _postToken(
      '/auth/register',
      {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation ?? password,
      },
      fallback: 'Could not create the account.',
    );
  }

  @override
  Future<void> logout() async {
    try {
      await _dio.post<dynamic>('/auth/logout');
    } on DioException catch (error) {
      throw AuthException(_messageFrom(error, 'Could not sign out.'));
    }
  }

  Future<String> _postToken(
    String path,
    Map<String, String> body, {
    required String fallback,
  }) async {
    try {
      final response = await _dio.post<dynamic>(path, data: body);
      return _tokenFrom(response.data);
    } on DioException catch (error) {
      throw AuthException(_messageFrom(error, fallback));
    }
  }

  String _tokenFrom(Object? data) {
    if (data is Map && data['token'] is String) {
      return data['token'] as String;
    }
    throw const AuthException('Invalid auth response.');
  }

  String _messageFrom(DioException error, String fallback) {
    final data = error.response?.data;
    if (data is Map) {
      final message = data['message'];
      if (message is String && message.isNotEmpty && message != 'The given data was invalid.') {
        return message;
      }
      final errors = data['errors'];
      if (errors is Map && errors.isNotEmpty) {
        final first = errors.values.first;
        if (first is List && first.isNotEmpty) {
          return first.first.toString();
        }
      }
      if (message is String && message.isNotEmpty) {
        return message;
      }
    }
    return fallback;
  }
}
