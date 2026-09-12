<?php
namespace App\Http\Controllers;
use App\Models\Expense;
use Illuminate\Http\Request;
class ExpenseController extends Controller
{
    public function index(){return view('expenses.index',['expenses'=>Expense::latest('expense_date')->paginate(20)]);}
    public function create(){return view('expenses.create');}
    public function store(Request $r){$d=$r->validate(['title'=>'required|string|max:255','description'=>'nullable|string','amount'=>'required|numeric|min:0','expense_date'=>'required|date']);Expense::create($d);return redirect()->route('expenses.index')->with('success','Pengeluaran disimpan.');}
    public function edit(Expense $expense){return view('expenses.create',compact('expense'));}
    public function update(Request $r,Expense $expense){$expense->update($r->validate(['title'=>'required|string|max:255','description'=>'nullable|string','amount'=>'required|numeric|min:0','expense_date'=>'required|date']));return redirect()->route('expenses.index')->with('success','Pengeluaran diperbarui.');}
    public function destroy(Expense $expense){$expense->delete();return back()->with('success','Pengeluaran dihapus.');}
}