import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';

import LoginPage from '../pages/login';
import AuthCallback from '../pages/authcallback';
import Dashboard from "../pages/dashboard.tsx";

export default function App() {

  const isAuthenticated = !!localStorage.getItem('auth_token');

  // 2. The Traffic Cop Rules
  return (
      <BrowserRouter>
        <Routes>

          <Route path="/auth-callback" element={<AuthCallback />} />
          <Route
              path="/login"
              element={isAuthenticated ? <Navigate to="/dashboard" replace /> : <LoginPage />}
          />

          <Route
              path="/dashboard"
              element={isAuthenticated ? <Dashboard /> : <Navigate to="/login" replace />}
          />

          <Route
              path="*"
              element={<Navigate to={isAuthenticated ? "/dashboard" : "/login"} replace />}
          />

        </Routes>
      </BrowserRouter>
  );
}