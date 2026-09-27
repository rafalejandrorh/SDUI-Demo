abstract class AuthRepository {
  Future<String> login({
    required String email,
    required String password,
  });

  Future<String> register({
    required String name,
    required String email,
    required String password,
    String? passwordConfirmation,
  });

  /// Revoke the server session (`POST /auth/logout`). No-op for mocks.
  Future<void> logout();
}
