import { Head } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Pagination } from "@/components/ui/pagination";
import { router } from "@inertiajs/react";
import { useState } from "react";
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from "@/components/ui/dialog";

interface Signup {
  id: number;
  name: string;
  email: string;
  verified_at: string | null;
  project_id: number;
  created_at: string;
  updated_at: string;
}

interface SignupsPagination {
  data: Signup[];
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}

interface Project {
  id: number;
  name: string;
  subdomain: string;
  description?: string;
  settings?: Record<string, any>;
  created_at: string;
  updated_at: string;
}

interface SignupIndexProps {
  project: Project;
  signups: SignupsPagination;
}

export default function SignupIndex({ project, signups }: SignupIndexProps) {
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [signupToDelete, setSignupToDelete] = useState<Signup | null>(null);

  const handleExport = () => {
    window.location.href = `/projects/${project.id}/signups/export`;
  };

  const confirmDelete = (signup: Signup) => {
    setSignupToDelete(signup);
    setDeleteDialogOpen(true);
  };

  const handleDelete = () => {
    if (!signupToDelete) return;

    router.delete(`/projects/${project.id}/signups/${signupToDelete.id}`, {
      onSuccess: () => {
        setDeleteDialogOpen(false);
        setSignupToDelete(null);
      },
    });
  };

  const handlePageChange = (page: number) => {
    router.get(
      `/projects/${project.id}/signups`,
      { page },
      { preserveState: true }
    );
  };

  return (
    <>
      <Head title={`${project.name} | Signups`}>
        <meta name="description" content={`Waitlist signups for ${project.name}`} />
      </Head>

      <div className="container py-8">
        <div className="flex justify-between items-center mb-6">
          <div>
            <h1 className="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
              Signups
            </h1>
            <p className="text-gray-600 dark:text-gray-400 mt-2">
              Manage everyone who has signed up to your waitlist.
            </p>
          </div>
          <Button onClick={handleExport}>
            Export as CSV
          </Button>
        </div>

        <Card>
          <CardHeader>
            <CardTitle>Waitlist Signups</CardTitle>
            <CardDescription>
              You have {signups.meta.total} total signups for your project.
            </CardDescription>
          </CardHeader>
          <CardContent>
            {signups.data.length > 0 ? (
              <>
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Name</TableHead>
                      <TableHead>Email</TableHead>
                      <TableHead>Signup Date</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {signups.data.map((signup) => (
                      <TableRow key={signup.id}>
                        <TableCell className="font-medium">{signup.name}</TableCell>
                        <TableCell>{signup.email}</TableCell>
                        <TableCell>
                          {new Date(signup.created_at).toLocaleDateString()}
                        </TableCell>
                        <TableCell>
                          {signup.verified_at ? (
                            <Badge variant="success">Verified</Badge>
                          ) : (
                            <Badge variant="secondary">Pending</Badge>
                          )}
                        </TableCell>
                        <TableCell className="text-right">
                          <Button
                            variant="destructive"
                            size="sm"
                            onClick={() => confirmDelete(signup)}
                          >
                            Delete
                          </Button>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>

                {signups.meta.last_page > 1 && (
                  <div className="mt-4 flex justify-end">
                    <Pagination
                      currentPage={signups.meta.current_page}
                      totalPages={signups.meta.last_page}
                      onPageChange={handlePageChange}
                    />
                  </div>
                )}
              </>
            ) : (
              <div className="text-center py-10">
                <p className="text-gray-500 dark:text-gray-400">
                  No signups yet. Share your waitlist page to get started.
                </p>
              </div>
            )}
          </CardContent>
        </Card>
      </div>

      <Dialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Confirm Deletion</DialogTitle>
            <DialogDescription>
              Are you sure you want to delete the signup for {signupToDelete?.name} ({signupToDelete?.email})?
              This action cannot be undone.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeleteDialogOpen(false)}>
              Cancel
            </Button>
            <Button variant="destructive" onClick={handleDelete}>
              Delete
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  );
}
