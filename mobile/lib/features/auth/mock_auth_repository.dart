import 'auth_exception.dart';
import 'auth_repository.dart';

class MockAuthRepository implements AuthRepository {
  static final _email = RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$');

  @override
  Future<String> login({
    required String email,
    required String password,
  }) async {
    await Future<void>.delayed(const Duration(milliseconds: 400));
    if (!_email.hasMatch(email) || password.length < 6) {
      throw const AuthException('Invalid email or password.');
    }
    return 'mock-token-${DateTime.now().millisecondsSinceEpoch}';
  }

  @override
  Future<String> register({
    required String name,
    required String email,
    required String password,
    String? passwordConfirmation,
  }) async {
    await Future<void>.delayed(const Duration(milliseconds: 400));
    if (name.trim().length < 2) {
      throw const AuthException('Enter your name.');
    }
    if (!_email.hasMatch(email)) {
      throw const AuthException('Enter a valid email.');
    }
    if (password.length < 6) {
      throw const AuthException('Password must be at least 6 characters.');
    }
    return 'mock-token-${DateTime.now().millisecondsSinceEpoch}';
  }

  @override
  Future<void> logout() async {}
}
