import React from 'react';
import { Box, Paper, Typography, Tooltip } from '@mui/material';
import { useGetManipulatorStateQuery, useGetSamplesQuery } from '../services/warehouseApi';

const GRID_SIZE = 10;
const CELL_SIZE = 50;

export const WarehouseGrid: React.FC = () => {
    const { data: manipulator } = useGetManipulatorStateQuery(undefined, { pollingInterval: 1000 });
    const { data: samples } = useGetSamplesQuery(undefined, { pollingInterval: 1000 });

    const renderCell = (x: number, y: number) => {
        const isFridge = x < 3 && y < 3;

        const isManipulator = manipulator && manipulator.x === x && manipulator.y === y;

        const sample = samples?.find(s => s.x === x && s.y === y && s.status === 'stored');

        return (
            <Box
                key={`${x}-${y}`}
                sx={{
                    width: CELL_SIZE,
                    height: CELL_SIZE,
                    border: '1px solid #ccc',
                    backgroundColor: isFridge ? '#e3f2fd' : 'white',
                    position: 'relative',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center'
                }}
            >
                {isFridge && <Typography variant="caption" sx={{ position: 'absolute', top: 0, left: 0, fontSize: 8, color: '#999' }}>F</Typography>}

                {sample && (
                    <Tooltip title={`${sample.name} (${sample.type})`}>
                        <Box
                            sx={{
                                width: 20,
                                height: 20,
                                borderRadius: '50%',
                                bgcolor: sample.type === 'cooling' ? 'primary.main' : 'warning.main',
                                border: sample.status === 'expired' ? '2px solid red' : 'none'
                            }}
                        />
                    </Tooltip>
                )}

                {isManipulator && (
                    <Box
                        sx={{
                            position: 'absolute',
                            width: 30,
                            height: 30,
                            border: '3px solid',
                            borderColor: 'secondary.main',
                            borderRadius: 1,
                            backgroundColor: manipulator.holding ? 'rgba(0,0,0,0.1)' : 'transparent',
                            zIndex: 2,
                        }}
                    />
                )}
            </Box>
        );
    };

    const rows = [];
    for (let r = GRID_SIZE - 1; r >= 0; r--) { // Iterate rows 9 down to 0 (top to bottom map)
        const cells = [];
        for (let c = 0; c < GRID_SIZE; c++) {
            cells.push(renderCell(c, r));
        }
        rows.push(
            <Box key={r} sx={{ display: 'flex' }}>
                {cells}
            </Box>
        );
    }

    return (
        <Paper elevation={3} sx={{ p: 2, display: 'inline-block' }}>
            <Typography variant="h6" gutterBottom>Warehouse Grid</Typography>
            <Box sx={{ border: '2px solid #333' }}>
                {rows}
            </Box>
        </Paper>
    );
};
