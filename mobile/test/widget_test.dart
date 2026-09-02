import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:sdui_app/features/auth/auth_controller.dart';
import 'package:sdui_app/features/auth/auth_scope.dart';
import 'package:sdui_app/features/auth/login_screen.dart';
import 'package:sdui_app/features/auth/mock_auth_repository.dart';
import 'package:sdui_client/sdui_client.dart';

void main() {
  testWidgets('LoginScreen shows Sign in title', (tester) async {
    final auth = AuthController(
      repository: MockAuthRepository(),
      tokenStore: MemoryTokenStore(),
    );

    await tester.pumpWidget(
      AuthScope(
        controller: auth,
        child: const MaterialApp(
          home: LoginScreen(),
        ),
      ),
    );

    expect(find.text('Sign in'), findsAtLeastNWidgets(1));
  });
}
