import React, { useState } from 'react';
import {
    Box, TextField, Button, Paper, Typography, Table, TableBody, TableCell, TableHead, TableRow, Chip, IconButton
} from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';
import { useAddSampleMutation, useExecuteCommandMutation, useGetHistoryQuery, useGetSamplesQuery, useDeleteSampleMutation } from '../services/warehouseApi';
import { useForm } from 'react-hook-form';

export const ControlPanel: React.FC = () => {
    const [command, setCommand] = useState('');
    const [compressed, setCompressed] = useState('');
    const [executeCommand] = useExecuteCommandMutation();
    const { data: history } = useGetHistoryQuery(undefined, { pollingInterval: 2000 });
    const { data: samples } = useGetSamplesQuery(undefined, { pollingInterval: 2000 });
    const [addSample] = useAddSampleMutation();
    const [deleteSample] = useDeleteSampleMutation();

    const { register, handleSubmit, reset } = useForm();

    const handleRun = async () => {
        if (!command) return;
        try {
            const res = await executeCommand(command).unwrap();
            setCompressed(res.compressed);
            setCommand('');
        } catch (err) {
            console.error('Failed to execute command:', err);
            alert('Failed to execute command. Check console for details.');
        }
    };

    const handleCreateSample = async (data: any) => {
        await addSample({
            name: data.name,
            type: data.type,
            status: 'stored',
            x: parseInt(data.x) || 0,
            y: parseInt(data.y) || 0
        });
        reset();
    };

    return (
        <Box sx={{ display: 'flex', gap: 2, flexDirection: 'column' }}>
            <Paper sx={{ p: 2 }}>
                <Typography variant="h6">Manipulator Control</Typography>
                <Box sx={{ display: 'flex', gap: 2, my: 2 }}>
                    <TextField
                        label="L,R,U,D, P(ick), E(ject)"
                        placeholder="e.g. 5R 2U P 2D E"
                        value={command}
                        onChange={(e) => setCommand(e.target.value.toUpperCase())}
                        fullWidth
                    />
                    <Button variant="contained" onClick={handleRun}>Run</Button>
                </Box>
                {compressed && <Typography>Last Compressed: <strong>{compressed}</strong></Typography>}
            </Paper>


            <Paper sx={{ p: 2 }}>
                <Typography variant="h6">Manage Samples</Typography>
                <form onSubmit={handleSubmit(handleCreateSample)}>
                    <Box sx={{ display: 'flex', gap: 2, my: 1, alignItems: 'center' }}>
                        <TextField size="small" label="Name" {...register('name', { required: true })} />
                        <TextField size="small" select SelectProps={{ native: true }} {...register('type')}>
                            <option value="normal">Normal</option>
                            <option value="cooling">Cooling</option>
                        </TextField>
                        <TextField size="small" type="number" label="X" {...register('x')} defaultValue={0} />
                        <TextField size="small" type="number" label="Y" {...register('y')} defaultValue={0} />
                        <Button type="submit" variant="outlined">Add</Button>
                    </Box>
                </form>

                <Table size="small" sx={{ mt: 2 }}>
                    <TableHead>
                        <TableRow><TableCell>Name</TableCell><TableCell>Type</TableCell><TableCell>Status</TableCell><TableCell>Pos</TableCell><TableCell>Expires</TableCell><TableCell>Action</TableCell></TableRow>
                    </TableHead>
                    <TableBody>
                        {samples?.map((s) => (
                            <TableRow key={s.id}>
                                <TableCell>{s.name}</TableCell>
                                <TableCell>{s.type}</TableCell>
                                <TableCell>
                                    <Chip label={s.status} size="small" color={s.status === 'expired' ? 'error' : 'default'} />
                                </TableCell>
                                <TableCell>{s.x !== null ? `${s.x},${s.y}` : '-'}</TableCell>
                                <TableCell>{s.expires_at || '-'}</TableCell>
                                <TableCell>
                                    <IconButton size="small" onClick={() => deleteSample(s.id)} color="error">
                                        <DeleteIcon />
                                    </IconButton>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </Paper>


            <Paper sx={{ p: 2, maxHeight: 300, overflow: 'auto' }}>
                <Typography variant="h6">Operation History</Typography>
                <Table size="small">
                    <TableHead>
                        <TableRow><TableCell>Action</TableCell><TableCell>Details</TableCell><TableCell>Time</TableCell></TableRow>
                    </TableHead>
                    <TableBody>
                        {history?.slice(0, 10).map((h: any) => (
                            <TableRow key={h.id}>
                                <TableCell>{h.action}</TableCell>
                                <TableCell>{h.details}</TableCell>
                                <TableCell>{new Date(h.created_at).toLocaleTimeString()}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </Paper>
        </Box>
    );
};
