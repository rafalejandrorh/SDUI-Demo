import 'package:flutter_test/flutter_test.dart';
import 'package:sdui_app/features/auth/auth_controller.dart';
import 'package:sdui_app/features/auth/auth_exception.dart';
import 'package:sdui_app/features/auth/auth_repository.dart';
import 'package:sdui_client/sdui_client.dart';

void main() {
  test('logout revokes on the repository then clears the token', () async {
    final tokens = MemoryTokenStore();
    await tokens.write('secret');
    final repository = _FakeAuthRepository();
    final auth = AuthController(repository: repository, tokenStore: tokens);

    await auth.restore();
    expect(auth.isLoggedIn, isTrue);

    await auth.logout();

    expect(repository.logoutCalls, 1);
    expect(auth.isLoggedIn, isFalse);
    expect(await tokens.read(), isNull);
  });

  test('logout still clears the session when revoke fails', () async {
    final tokens = MemoryTokenStore();
    await tokens.write('secret');
    final repository = _FakeAuthRepository()..logoutError = true;
    final auth = AuthController(repository: repository, tokenStore: tokens);

    await auth.restore();
    await auth.logout();

    expect(auth.isLoggedIn, isFalse);
    expect(await tokens.read(), isNull);
  });

  test('clearSession does not call logout on the API', () async {
    final tokens = MemoryTokenStore();
    await tokens.write('secret');
    final repository = _FakeAuthRepository();
    final auth = AuthController(repository: repository, tokenStore: tokens);

    await auth.restore();
    await auth.clearSession();

    expect(repository.logoutCalls, 0);
    expect(auth.isLoggedIn, isFalse);
  });
}

class _FakeAuthRepository implements AuthRepository {
  int logoutCalls = 0;
  bool logoutError = false;

  @override
  Future<String> login({
    required String email,
    required String password,
  }) async {
    return 'token';
  }

  @override
  Future<String> register({
    required String name,
    required String email,
    required String password,
    String? passwordConfirmation,
  }) async {
    return 'token';
  }

  @override
  Future<void> logout() async {
    logoutCalls++;
    if (logoutError) {
      throw const AuthException('nope');
    }
  }
}
