export interface IAssignment {
  id: string;
  activity: {
    id: string;
    title: string;
    status: 'PENDING' | 'IN_PROGRESS' | 'COMPLETED' | 'CANCELLED';
    scheduledStart: string;
    scheduledEnd: string | null;
  };
  technician: {
    id: string;
    name: string;
    email: string;
  };
  assignedBy: {
    id: string;
    name: string;
    email: string;
  };
  notes: string | null;
  assignedAt: string;
  createdAt: string;
  updatedAt: string;
}

export interface ICreateAssignmentRequest {
  activityId: string;
  technicianId: string;
  notes?: string;
}

export interface IUpdateAssignmentRequest {
  notes?: string;
}
