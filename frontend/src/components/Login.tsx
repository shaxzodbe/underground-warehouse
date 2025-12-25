import React from 'react';
import { useForm } from 'react-hook-form';
import { Box, Button, TextField, Paper, Typography, Container } from '@mui/material';

interface LoginProps {
    onLogin: (token: string) => void;
}

export const Login: React.FC<LoginProps> = ({ onLogin }) => {
    const { register, handleSubmit } = useForm();

    const onSubmit = (data: any) => {
        if (data.username && data.password) {
            onLogin('mock-token');
        }
    };

    return (
        <Box sx={{ minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center', bgcolor: 'background.default' }}>
            <Container maxWidth="xs">
                <Paper elevation={3} sx={{ p: 4, display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                    <Typography component="h1" variant="h5" gutterBottom>
                        Warehouse Login
                    </Typography>
                    <Box component="form" onSubmit={handleSubmit(onSubmit)} sx={{ mt: 1, width: '100%' }}>
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            label="Username"
                            autoComplete="username"
                            autoFocus
                            {...register('username')}
                        />
                        <TextField
                            margin="normal"
                            required
                            fullWidth
                            label="Password"
                            type="password"
                            autoComplete="current-password"
                            {...register('password')}
                        />
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            sx={{ mt: 3, mb: 2 }}
                            size="large"
                        >
                            Log In
                        </Button>
                    </Box>
                </Paper>
            </Container>
        </Box>
    );
};
