import { useState, useEffect } from "react";
import { toast } from "sonner";
import { Toaster } from "@/components/ui/sonner";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Trash2, Plus, Loader2, CheckCircle2, Moon, Sun, LayoutList, Sparkles } from "lucide-react";

interface Todo {
	id: number;
	title: string;
	completed: boolean;
	created_at: string;
	updated_at: string;
}

const API_BASE = "/api/v1";

function App() {
	const [todos, setTodos] = useState<Todo[]>([]);
	const [newTodo, setNewTodo] = useState("");
	const [loading, setLoading] = useState(true);
	const [isDark, setIsDark] = useState(true);

	useEffect(() => {
		document.documentElement.classList.toggle("dark", isDark);
	}, [isDark]);

	useEffect(() => {
		fetchTodos();
	}, []);

	const fetchTodos = async () => {
		try {
			const response = await fetch(`${API_BASE}/todos`);
			if (!response.ok) throw new Error("Failed to fetch todos");
			const data = await response.json();
			setTodos(data);
		} catch (error) {
			console.error("Error fetching todos:", error);
			toast.error("Failed to load todos");
		} finally {
			setLoading(false);
		}
	};

	const addTodo = async (e: React.FormEvent) => {
		e.preventDefault();
		if (!newTodo.trim()) return;

		try {
			const response = await fetch(`${API_BASE}/todos`, {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ title: newTodo }),
			});
			if (!response.ok) throw new Error("Failed to add todo");
			const addedTodo = await response.json();
			setTodos([addedTodo, ...todos]);
			setNewTodo("");
			toast.success("Task added");
		} catch (error) {
			toast.error("Error adding task");
		}
	};

	const toggleTodo = async (id: number, completed: boolean) => {
		try {
			const response = await fetch(`${API_BASE}/todos/${id}`, {
				method: "PUT",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({ completed: !completed }),
			});
			if (!response.ok) throw new Error("Failed to update todo");
			const updatedTodo = await response.json();
			setTodos(todos.map((t) => (t.id === id ? updatedTodo : t)));
		} catch (error) {
			toast.error("Failed to update");
		}
	};

	const deleteTodo = async (id: number) => {
		try {
			const response = await fetch(`${API_BASE}/todos/${id}`, {
				method: "DELETE",
			});
			if (!response.ok) throw new Error("Failed to delete todo");
			setTodos(todos.filter((t) => t.id !== id));
			toast.success("Task removed");
		} catch (error) {
			toast.error("Failed to delete");
		}
	};

	const completedCount = todos.filter((t) => t.completed).length;

	return (
		<main className="bg-background relative flex min-h-screen flex-col items-center justify-start overflow-hidden px-4 py-4 transition-colors duration-700">
			{/* Dynamic Mesh Background */}
			<div className="pointer-events-none absolute inset-0 overflow-hidden">
				<div className="absolute -top-[10%] -left-[10%] h-[50%] w-[50%] rounded-full bg-violet-400/10 blur-[100px] dark:bg-violet-600/10"></div>
				<div className="absolute top-[30%] -right-[10%] h-[40%] w-[40%] rounded-full bg-indigo-400/10 blur-[100px] dark:bg-indigo-600/10"></div>
			</div>

			{/* Theme Toggle Button */}
			<button onClick={() => setIsDark(!isDark)} className="border-border bg-card/50 text-foreground hover:bg-card absolute top-6 right-6 z-50 flex h-12 w-12 items-center justify-center rounded-2xl border shadow-sm backdrop-blur-md transition-all active:scale-95">
				{isDark ? <Sun className="h-6 w-6 text-yellow-500" /> : <Moon className="h-6 w-6 text-slate-700" />}
			</button>

			<div className="relative z-10 w-full max-w-lg">
				{/* Header Section */}
				<header className="mb-6 text-center">
					<div className="border-primary/20 bg-primary/5 text-primary mb-4 inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-[10px] font-bold tracking-wider uppercase">
						<Sparkles className="h-3 w-3" />
						<span>Robust PHP Framework</span>
					</div>
					<h1 className="text-foreground flex items-center justify-center gap-3 text-5xl font-black tracking-tight sm:text-6xl">
						<LayoutList className="text-primary h-12 w-12" />
						TaskFlow
					</h1>
					<p className="text-muted-foreground mt-4 text-base font-medium">Eloquent ORM & React Integration</p>
				</header>

				{/* Main Card */}
				<Card className="border-border/50 bg-card/80 gap-3 shadow-2xl backdrop-blur-2xl transition-all">
					<CardHeader className="pb-0">
						<div className="flex items-center justify-between">
							<CardTitle className="text-foreground text-xl font-extrabold">Active Queue</CardTitle>
							<div className="bg-muted text-muted-foreground rounded-lg px-2 py-1 text-[10px] font-bold">
								{completedCount} / {todos.length}
							</div>
						</div>
					</CardHeader>
					<CardContent className="space-y-6">
						{/* Input Section */}
						<form onSubmit={addTodo} className="flex gap-3">
							<Input placeholder="What's next on your list?" value={newTodo} onChange={(e) => setNewTodo(e.target.value)} className="border-border bg-background focus-visible:ring-primary h-14 text-base shadow-inner" />
							<Button type="submit" size="icon" className="h-14 w-14 rounded-2xl shadow-xl transition-all hover:scale-105 active:scale-95">
								<Plus className="h-7 w-7" />
							</Button>
						</form>

						{/* List Section */}
						<div className="space-y-3">
							{loading ? (
								<div className="text-muted-foreground flex flex-col items-center justify-center py-16">
									<Loader2 className="text-primary mb-4 h-10 w-10 animate-spin" />
									<p className="text-sm font-semibold tracking-wide">Fetching Tasks...</p>
								</div>
							) : todos.length === 0 ? (
								<div className="border-border/50 rounded-3xl border-2 border-dashed py-16 text-center">
									<div className="bg-muted text-muted-foreground/50 mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl">
										<CheckCircle2 className="h-8 w-8" />
									</div>
									<p className="text-muted-foreground text-sm font-bold">Pure Zen.</p>
								</div>
							) : (
								<div className="custom-scrollbar max-h-[460px] space-y-3 overflow-y-auto pr-2">
									{todos.map((todo) => (
										<div key={todo.id} className="group border-border bg-card/50 hover:border-primary/40 hover:bg-card flex items-center justify-between rounded-2xl border p-5 transition-all hover:shadow-lg">
											<div className="flex items-center gap-5">
												<div className="flex items-center">
													<Checkbox id={`todo-${todo.id}`} checked={todo.completed} onCheckedChange={() => toggleTodo(todo.id, todo.completed)} className="border-border data-[state=checked]:bg-primary data-[state=checked]:border-primary h-6 w-6 rounded-lg border-2" />
												</div>
												<Label htmlFor={`todo-${todo.id}`} className={`cursor-pointer text-lg font-bold transition-all duration-500 ${todo.completed ? "text-muted-foreground/40 line-through" : "text-foreground"}`}>
													{todo.title}
												</Label>
											</div>
											<Button variant="ghost" size="icon" onClick={() => deleteTodo(todo.id)} className="hover:bg-destructive/10 hover:text-destructive h-10 w-10 rounded-xl opacity-0 transition-all group-hover:opacity-100">
												<Trash2 className="h-5 w-5" />
											</Button>
										</div>
									))}
								</div>
							)}
						</div>
					</CardContent>
				</Card>

				<footer className="text-muted-foreground/40 mt-10 flex flex-col items-center gap-2 text-xs font-bold tracking-wider uppercase transition-colors">
					<p className="flex items-center gap-1">
						Made with ❤️ by
						<a href="https://t.me/mrbeandev" target="_blank" rel="noopener noreferrer" className="text-primary hover:text-primary/80 transition-all hover:underline">
							@mrbeandev
						</a>
					</p>
				</footer>
			</div>

			<Toaster richColors position="top-right" />
		</main>
	);
}

export default App;
