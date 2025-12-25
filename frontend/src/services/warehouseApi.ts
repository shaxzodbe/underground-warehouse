import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';

export interface Sample {
    id: number;
    name: string;
    type: 'normal' | 'cooling';
    status: 'stored' | 'held' | 'dropped' | 'expired';
    created_at: string;
    expires_at: string | null;
    x: number | null;
    y: number | null;
}

export interface ManipulatorState {
    x: number;
    y: number;
    holding: number | null;
}

export interface CommandResponse {
    status: string;
    state: ManipulatorState;
    compressed: string;
}

export const warehouseApi = createApi({
    reducerPath: 'warehouseApi',
    baseQuery: fetchBaseQuery({ baseUrl: import.meta.env.VITE_API_URL || 'http://localhost:3884/' }),
    tagTypes: ['Sample', 'Manipulator', 'History'],
    endpoints: (builder) => ({
        getSamples: builder.query<Sample[], void>({
            query: () => 'sample',
            providesTags: ['Sample'],
        }),
        addSample: builder.mutation<Sample, Partial<Sample>>({
            query: (body) => ({
                url: 'sample',
                method: 'POST',
                body,
            }),
            invalidatesTags: ['Sample'],
        }),
        deleteSample: builder.mutation<void, number>({
            query: (id) => ({
                url: `sample/${id}`,
                method: 'DELETE',
            }),
            invalidatesTags: ['Sample'],
        }),
        getManipulatorState: builder.query<ManipulatorState, void>({
            query: () => 'manipulator',
            providesTags: ['Manipulator'],
        }),
        executeCommand: builder.mutation<CommandResponse, string>({
            query: (command) => ({
                url: 'manipulator',
                method: 'POST',
                body: { command },
            }),
            invalidatesTags: ['Manipulator', 'Sample', 'History'],
        }),
        getHistory: builder.query<any[], void>({
            query: () => 'history',
            providesTags: ['History'],
        }),
    }),
});

export const {
    useGetSamplesQuery,
    useAddSampleMutation,
    useDeleteSampleMutation,
    useGetManipulatorStateQuery,
    useExecuteCommandMutation,
    useGetHistoryQuery
} = warehouseApi;
