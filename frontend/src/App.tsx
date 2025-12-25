import React, { useState } from 'react';
import { Provider } from 'react-redux';
import { store } from './store';
import { Box, Container, CssBaseline, Typography, AppBar, Toolbar } from '@mui/material';
import { WarehouseGrid } from './components/WarehouseGrid';
import { ControlPanel } from './components/ControlPanel';
import { Login } from './components/Login';

function App() {
  const [token, setToken] = useState<string | null>(localStorage.getItem('token'));

  const handleLogin = (t: string) => {
    localStorage.setItem('token', t);
    setToken(t);
  };

  if (!token) {
    return <Login onLogin={handleLogin} />;
  }

  return (
    <Provider store={store}>
      <CssBaseline />
      <AppBar position="static">
        <Toolbar>
          <Typography variant="h6">Underground Warehouse</Typography>
        </Toolbar>
      </AppBar>
      <Container maxWidth="xl" sx={{ mt: 4 }}>
        <Box sx={{ display: 'flex', gap: 4, flexDirection: { xs: 'column', md: 'row' } }}>
          <WarehouseGrid />
          <Box sx={{ flex: 1 }}>
            <ControlPanel />
          </Box>
        </Box>
      </Container>
    </Provider>
  );
}

export default App;
